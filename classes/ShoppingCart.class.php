<?php
/**
 * Shopping Cart
 *
 * The Shopping Cart of TheCartPress
 *
 * @package TheCartPress
 * @subpackage Classes
 */

/**
 * This file is part of TheCartPress.
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ShoppingCart' ) ) :

require_once( 'ICartSource.interface.php' );

/**
 * Session Shopping Cart
 */
class ShoppingCart {

	public static $OTHER_COST_SHIPPING_ID	= 'shipping';
	public static $OTHER_COST_PAYMENT_ID	= 'payment';

	private $visited_post_ids		= array();
	private $wish_list_post_ids		= array();
	private $shopping_cart_items	= array();
	private $other_costs			= array();
	private $freeShipping			= false;
	private $discounts				= array();
	private $order_id				= 0;

	/**
	 * Adds a product in the shopping cart
	 *
	 * @uses apply_filters, calls 'tcp_add_to_shopping_cart
	 */
	function add( $post_id, $option_1_id = 0, $option_2_id = 0, $count = 1, $unit_price = 0, $unit_weight = 0 ) {
		if ( !is_numeric( $post_id ) || !is_numeric( $option_1_id ) || !is_numeric( $option_2_id ) ) {
			return;
		}
		$shopping_cart_id = $post_id . '_' . $option_1_id . '_' . $option_2_id;
		$shopping_cart_id = sanitize_key( apply_filters( 'tcp_shopping_cart_key', $shopping_cart_id ) );

		// If the product is in the cart, only add more units
		if ( isset( $this->shopping_cart_items[$shopping_cart_id] ) ) {
			$sci = $this->shopping_cart_items[$shopping_cart_id];
			$sci->add( $count );
		} else {
			$sci = new ShoppingCartItem( $post_id, $option_1_id, $option_2_id, $count, $unit_price, $unit_weight );
		}
		$sci = apply_filters( 'tcp_add_to_shopping_cart', $sci, $count, $unit_price, $unit_weight );
		if ( is_wp_error( $sci ) || $sci === false ) {
			return $sci;
		} else {
			$this->shopping_cart_items[$shopping_cart_id] = $sci;
		}
		$this->removeOrderId();

		// Sends an action: an item has been modified in the shopping cart
		do_action( 'tcp_shopping_cart_item_added', $sci, $this );
		return $sci;
	}

	function modify( $post_id, $option_1_id = 0, $option_2_id = 0, $count = 0 ) {
		$count = (int)$count;
		$shopping_cart_id = $post_id . '_' . $option_1_id . '_' . $option_2_id;
		$shopping_cart_id = sanitize_key( apply_filters( 'tcp_shopping_cart_key', $shopping_cart_id ) );
		$sci = false;
		if ( isset( $this->shopping_cart_items[$shopping_cart_id] ) ) {
			if ( $count > 0 ) {
				$sci = $this->shopping_cart_items[$shopping_cart_id];
				$sci->setUnits( $count );
				$sci = apply_filters( 'tcp_modify_to_shopping_cart', $sci, $count );
				if ( is_wp_error( $sci ) || $sci === false ) {
					return $sci;
				} else {
					$this->shopping_cart_items[$shopping_cart_id] = $sci;
				}
				// Sends an action: an item has been modified in the shopping cart
				do_action( 'tcp_shopping_cart_item_modified', $post_id, $this );
			} else {
				$this->delete( $post_id, $option_1_id , $option_2_id );
			}
			$this->removeOrderId();
		}
		return $sci;
	}

	/**
	 * Removes a given product from the cart
	 *
	 * @param int $post_id product identifier
	 * @param int $option_1_id (deprecated)
	 * @param int $option_2_id (deprecated)
	 *
	 * @uses sanitize_key, apply_filters ('tcp_shopping_cart_key'), ShoppingCart::removeOrderId
	 */
	function delete( $post_id, $option_1_id = 0, $option_2_id = 0 ) {
		$shopping_cart_id = $post_id . '_' . $option_1_id . '_' . $option_2_id;
		$shopping_cart_id = sanitize_key( apply_filters( 'tcp_shopping_cart_key', $shopping_cart_id ) );
		if ( isset( $this->shopping_cart_items[$shopping_cart_id] ) )
			unset( $this->shopping_cart_items[$shopping_cart_id] );
		$this->removeOrderId();

		// Sends an action: an item has been deleted from the shopping cart
		do_action( 'tcp_shopping_cart_item_deleted', $post_id, $this );
	}

	function deleteAll() {
		unset( $this->shopping_cart_items );
		$this->shopping_cart_items = array();
		unset( $this->other_costs );
		$this->other_costs = array();
		$this->deleteAllDiscounts();
		//$this->removeOrderId();

		// Sends an action: the shopping cart has been deleted
		do_action( 'tcp_shopping_cart_all_deleted', $this );
	}

	function refresh() {
		$items = $this->getItems();
		$this->deleteAll();
		foreach( $items as $item ) {
			$price = tcp_get_the_price( $item->getPostId() );
			if ( $item->getOption1Id() > 0 ) $price += tcp_get_the_price( $item->getOption1Id() );
			if ( $item->getOption2Id() > 0 ) $price += tcp_get_the_price( $item->getOption2Id() );
			$sci = $this->add( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id(), $item->getCount(), $price, $item->getWeight() );
			$sci->set_attributes( $item->get_attributes() );
		}
		$this->removeOrderId();
	}

	function getItemsId() {
		$ids = array();
		foreach( $this->shopping_cart_items as $id => $item ) {
			$ids[] = $item->getPostId();
		}
		return $ids;
	}

	function getItems() {
		$items = $this->shopping_cart_items;
		return apply_filters( 'tcp_shopping_cart_get_items', $items );
	}

	/**
	 * Returns and item if it's in the cart
	 */
	function getItem( $post_id, $option_1_id = 0 , $option_2_id = 0 ) {
		$shopping_cart_id = $post_id . '_' . $option_1_id . '_' . $option_2_id;
		$shopping_cart_id = sanitize_key( apply_filters( 'tcp_shopping_cart_key', $shopping_cart_id ) );
		if ( isset( $this->shopping_cart_items[$shopping_cart_id] ) ) {
			return $this->shopping_cart_items[$shopping_cart_id];
		} elseif ( $option_1_id == 0 && $option_2_id == 0) {
			foreach( $this->shopping_cart_items as $item ) {
				if ( $item && $item->getPostId() == $post_id ) {
					return $item;
				}
			}
			return false;
		} else {
			return false;
		}
	}

	/**
	 * Returns and item if it's in the cart
	 * @since 1.2.5
	 */
	function getItemBySku( $sku ) {
		foreach( $this->shopping_cart_items as $item )
			if ( $item->getSku() == $sku ) return $item;
		return false;
	}

	/**
	 * Delete item by post_id
	 *
	 * @since 1.2.8
	 */
	function deleteItem( $post_id, $option_1_id = 0 , $option_2_id = 0 ) {
		foreach( $this->shopping_cart_items as $id => $item ) {
			if ( $item->getPostId() == $post_id && $item->getOption1Id() == $option_1_id && $item->getOption2Id() == $option_2_id ) {
				unset( $this->shopping_cart_items[$id] );

				// Sends an action: an item has been deleted from the shopping cart
				do_action( 'tcp_shopping_cart_item_deleted', $post_id, $this );
				return true;
			}
		}
		return false;
	}

	/**
	 * Add an item
	 *
	 * @since 1.2.9
	 */
	function addItem( $item, $shopping_cart_id = false ) {
		if ( $shopping_cart_id === false ) {
			$this->shopping_cart_items[] = $item;
		} else {
			if ( isset( $this->shopping_cart_items[$shopping_cart_id] ) ) {
				$sci = $this->shopping_cart_items[$shopping_cart_id];
				$sci->add( $item->getUnits() );
			} else {
				$this->shopping_cart_items[$shopping_cart_id] = $item;
			}
		}
		// Sends an action: an item has been modified in the shopping cart
		do_action( 'tcp_shopping_cart_item_added', $item, $this );
	}

	/**
	 * Add items
	 * @since 1.2.9
	 */
	function setItems( $items ) {
		$this->shopping_cart_items = $items;

		// Sends an action: an item has been modified in the shopping cart
		do_action( 'tcp_shopping_cart_items_modified', $items, $this );
	}

	/**
	 * Returns the total amount in the cart
	 * @see getTotalForShipping()
	 */
	function getTotal( $otherCosts = false ) {
		$total = 0;
		$items = $this->getItems();
		foreach( $items as $item ) {
			$total += $item->getTotal();
		}
		if ( $otherCosts ) $total += $this->getTotalOtherCosts();
		$total -= $this->getCartDiscountsTotal();
		$total = (float)apply_filters( 'tcp_shopping_cart_get_total', $total );
		return $total;
	}

	/**
	 * Returns the total amount to calculate shipping cost
	 */
	function getTotalForShipping() {
		$total = 0;
		foreach( $this->shopping_cart_items as $item ) {
			if ( ! $item->isDownloadable() && ! $item->isFreeShipping() ) {
				$total += $item->getTotal();
			}
		}
		return $total;
	}

	function getTotalToShow( $otherCosts = false ) {
		$total = 0;
		foreach( $this->shopping_cart_items as $shopping_cart_item ) {
			if ( $shopping_cart_item ) {
				$total += $shopping_cart_item->getTotalToShow();
			}
		}
		if ( $otherCosts ) {
			$total += $this->getTotalOtherCosts();
		}
		$total -= $this->getCartDiscountsTotal();
		$total = (float)apply_filters( 'tcp_shopping_cart_get_total_to_show', $total );
		return $total;
	}

	/**
	 * Returns the number of articles in the cart
	 *
	 * @uses ShoppingCartItem::getCount
	 */
	function getCount() {
		$count = 0;
		foreach( $this->shopping_cart_items as $shopping_cart_item ) {
			$count += $shopping_cart_item->getCount();
		}
		return $count;
	}

	/**
	 * Returns the total weight of products in the cart.
	 *
	 * @uses ShoppingCartItem::getWeight
	 */
	function getWeight() {
		$weight = 0;
		foreach( $this->shopping_cart_items as $shopping_cart_item ) {
			$weight += $shopping_cart_item->getWeight();
		}
		return $weight;
	}

	/**
	 * Returns the total weight of products in the cart.
	 * It's used to calculate shipping costs, so free products are not added
	 *
	 * @uses ShoppingCartItem::isDownloadable, ShoppingCartItem::isFreeShipping, ShoppingCartItem::getWeight
	 */
	function getWeightForShipping() {
		$weight = 0;
		foreach( $this->shopping_cart_items as $item ) {
			if ( ! $item->isDownloadable() && ! $item->isFreeShipping() ) {
				$weight += $item->getWeight();
			}
		}
		return $weight;
	}

	/**
	 * Returns true if the cart is empty
	 */
	function isEmpty() {
		return count( $this->shopping_cart_items ) == 0;
	}

	/**
	 * Returns true if a given product exists in the cart
	 *
	 * @since 1.0
	 *
	 * @param $post_id given product id
	 * @param $option_1_id (deprecated)
	 * @param $option_2_id (deprecated)
	 * @return boolean, true if the given product exists in the cart
	 * @uses sanitize_key, apply_filters ('tcp_shopping_cart_key')
	 */
	function exists( $post_id, $option_1_id = 0 , $option_2_id = 0 ) {
		$shopping_cart_id = $post_id . '_' . $option_1_id . '_' . $option_2_id;
		$shopping_cart_id = sanitize_key( apply_filters( 'tcp_shopping_cart_key', $shopping_cart_id ) );
		return isset( $this->shopping_cart_items[$shopping_cart_id] );
	}

	/**
	 * Returns true if all products, in the cart, are downloadable
	 *
	 * @since 1.0
	 *
	 * @return boolean, true if all products in the cart are downloadable
	 * @uses ShoppingCartIte::isDownloadable
	 */
	function isDownloadable() {
		foreach( $this->shopping_cart_items as $item ) {
			if ( ! $item->isDownloadable() ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Returns true if anyone of the products, in the cart, is downloadable
	 *
	 * @since 1.2.9 support filter 'tcp_has_downloadable'
	 *
	 * @uses ShoppingCartIte::isDownloadable, apply_filters ('tcp_has_downloadable')
	 */
	function hasDownloadable() {
		$has_downloadable = false;
		foreach( $this->shopping_cart_items as $item ) {
			if ( $item->isDownloadable() ) {
				$has_downloadable = true;
				break;
			}
		}
		return apply_filters( 'tcp_has_downloadable', $has_downloadable );
	}

	/**
	 * Set Order_id
	 * It's used if the cart has been saved in the database
	 *
	 * @since 1.1.0
	 *
	 * @param int $order_id 
	 */
	function setOrderId( $order_id ) {
		$this->order_id = $order_id;
		return $this;
	}

	function getOrderId() {
		return $this->order_id;
	}

	function hasOrderId() {
		return $this->order_id > 0;
	}

	function removeOrderId() {
		return $this->setOrderId(0);
	}

	/**
	 * Adds a given product to the visited list
	 *
	 * @since 1.2
	 *
	 * @param $post_id product id to add
	 * @uses ShoppingCart::getVisitedPosts
	 */
	function addVisitedPost( $post_id ) {
		if ( isset( $this->visited_post_ids[$post_id] ) ) {
			$this->visited_post_ids[$post_id]++;
		} else {
			$this->visited_post_ids[$post_id] = 0;
		}
		return $this->getVisitedPosts();
	}

	function getVisitedPosts() {
		return $this->visited_post_ids;
	}

	function deleteVisitedPost() {
		unset( $this->visited_post_ids );
		$this->visited_post_ids = array();
	}
	/**
	 * End Visited functions
	 */

	/**
	 * WishList functions
	 */
	function addWishList( $post_id ) {
		$user_id = get_current_user_id();
		if ( $user_id > 0 ) {	
			$wishList = (array)get_user_meta( $user_id, 'tcp_wish_list', true );
			$wishList[$post_id] = 1;
			update_user_meta( $user_id, 'tcp_wish_list', $wishList );
		} else {
			$this->wish_list_post_ids[$post_id] = 1;
		}
	}

	function isInWishList( $post_id ) {
		$user_id = get_current_user_id();
		if ( $user_id > 0 ) {	
			$wishList =  (array)get_user_meta( $user_id, 'tcp_wish_list', true );
			return isset( $wishList[$post_id] );
		} else {
			return isset( $this->wish_list_post_ids[$post_id] );
		}
	}

	function getWishList() {
		$user_id = get_current_user_id();
		if ( $user_id > 0 ) {	
			$wishList = (array)get_user_meta( $user_id, 'tcp_wish_list', true );
			if ( count( $this->wish_list_post_ids ) > 0 ) {
				foreach( $this->wish_list_post_ids as $id => $item ) {
					$wishList[$id] = 1;
				}
				update_user_meta( $user_id, 'tcp_wish_list', $wishList );
				unset( $this->wish_list_post_ids );
				$this->wish_list_post_ids = array();
			}
			return $wishList;
		} else {
			return $this->wish_list_post_ids;
		}
	}

	function deleteWishListItem( $post_id) {
		$user_id = get_current_user_id();
		if ( $user_id > 0 ) {	
			$wishList = (array)get_user_meta( $user_id, 'tcp_wish_list', true );
			unset( $wishList[$post_id] );
			update_user_meta( $user_id, 'tcp_wish_list', $wishList );
		} else {
			unset( $this->wish_list_post_ids[$post_id] );
		}
	}

	function deleteWishList() {
		$user_id = get_current_user_id();
		if ( $user_id > 0 ) {	
			update_user_meta( $user_id, 'tcp_wish_list', array() );
		} else {
			unset( $this->wish_list_post_ids );
			$this->wish_list_post_ids = array();
		}
	}

/*	function volcarWishList() {
		$current_user = wp_get_current_user();
		if ( $current_user->ID > 0 ) {
			update_user_meta( $current_user->ID, 'tcp_wish_list', $this->wish_list_post_ids );
			return true;
		} else {
			return false;
		}
	}*/
	/**
	 * End WishList functions
	 */
	
	/**
	 * Other costs API
	 */
	function addOtherCost( $id, $cost = 0, $desc = '', $order = 0 ) {
		if ( $cost == 0 ) {
			$this->deleteOtherCost( $id );
		} else {
			$this->other_costs[$id] = new ShoppingCartOtherCost( $cost, $desc, $order );
		}
	}

	function deleteOtherCost( $id, $starts = false ) {
		if ( $starts ) {
			foreach( $this->other_costs as $cost_id => $cost ) {
				if ( ! strncmp( $cost_id, $id, strlen( $id ) ) ) {
					unset( $this->other_costs[$cost_id] );
				}
			}
		} else {
			if ( isset( $this->other_costs[$id] ) ) unset( $this->other_costs[$id] );
		}
	}

	function getOtherCosts() {
		return $this->other_costs;
	}

	function getOtherCostById( $id ) {
		$otherCost = $this->getOtherCosts();
		return isset( $otherCost[$id] ) ? $otherCost[$id] : false;
	}

	function deleteOtherCosts() {
		unset( $this->other_costs );
		$this->other_costs = array();
	}

	function getTotalOtherCosts() {
		$total = 0;
		foreach( $this->other_costs as $other_cost ) {
			$total += $other_cost->getCost();
		}
		return $total;
	}
	
	function setFreeShipping( $freeShipping = true ) {
		$this->freeShipping = (bool)$freeShipping;
		if ( $freeShipping ) {
			$this->deleteOtherCost( ShoppingCart::$OTHER_COST_SHIPPING_ID );
		}
	}

	function isFreeShipping() {
		return $this->freeShipping;
	}

	/**
	 * Returns the total of discounts in the cart (not for each product)
	 */
	function getCartDiscountsTotal() {
		$discount = 0;
		foreach( $this->discounts as $discount_item ) {
			$discount += $discount_item->getDiscount();
		}
		$discount = (float)apply_filters( 'tcp_shopping_cart_get_cart_discounts', $discount );
		return $discount;
	}

	/**
	 * Returns the total of discounts in the cart (not for each product)
	 */
	function getCartDiscounts() {
		return $this->discounts;
	}

	/**
	 * Adds a cart discount
	 */
	function addDiscount( $id, $discount = 0, $desc = '' ) {
		if ( $discount == 0 ) {
			$this->deleteDiscount( $id );
		} else {
			$discount = new ShoppingCartDiscount( $discount, $desc );
			$this->discounts[$id] = $discount;
		}
	}

	/**
	 * Deletes a cart discount
	 */
	function deleteDiscount( $id ) {
		if ( isset( $this->discounts[$id] ) ) {
			unset( $this->discounts[$id] );
		}
	}

	/**
	 * Deletes all cart discounts
	 */
	function deleteAllCartDiscounts() {
		unset( $this->discounts );
		$this->discounts = array();
	}

	/**
	 * Returns the total of discounts in the cart (product discount + cart discounts)
	 */
	function getAllDiscounts() {
		$discount = $this->getCartDiscountsTotal();
		foreach( $this->shopping_cart_items as $item ) {
			if ( $item ) {
				$discount += $item->getDiscount();
			}
		}
		return apply_filters( 'tcp_get_all_discounts', $discount );
	}

	/**
	 * Deletes all discounts
	 */
	function deleteAllDiscounts() {
		foreach( $this->shopping_cart_items as $item ) {
			$item->setDiscount( 0 );
		}
		$this->deleteAllCartDiscounts();
	}
}

class ShoppingCartItem implements TCP_IDetailSource {
	private $post_id;
	private $option_1_id;
	private $option_2_id;
	private $count;
	private $unit_price;
	private $tax = false;
	private $unit_weight;
	private $sku = false; //@since 1.2.5
	private $is_downloadable = false;
	private $discount = 0;
	private $discount_desc = '';//not in use
	private $free_shipping = false;
	private $attributes = array();

	function __construct( $post_id, $option_1_id = 0, $option_2_id = 0, $count = 1, $unit_price = 0, $unit_weight = 0 ) {
		$this->post_id = $post_id;
		$this->option_1_id = $option_1_id;
		$this->option_2_id = $option_2_id;
		$this->count = (int)$count;
		$this->unit_price = round( $unit_price, tcp_get_decimal_currency() );
		$this->unit_weight = $unit_weight;
		$this->setSku( tcp_get_the_sku( $post_id, $option_1_id, $option_2_id ) );
		do_action( 'tcp_shopping_cart_item_created', $this );
	}

	function add( $count ) {
		$this->count += $count;
	}

	function getShoppingCartId() {
		return $this->post_id . '_' . $this->option_1_id . '_' . $this->option_2_id;
	}

	// To implement TCP_IDetailSource
	function get_post_id() {
		return $this->getPostId();
	}

	function getPostId() {
		return $this->post_id;
	}

	// To implement TCP_IDetailSource
	function get_option_1_id() {
		return $this->getOption1Id();
	}

	function getOption1Id() {
		return $this->option_1_id;
	}

	function getOption2Id() {
		return $this->option_2_id;
	}

	// To implement TCP_IDetailSource
	function get_option_2_id() {
		return $this->getOption2Id();
	}

	function getTitle() {
		return tcp_get_the_title( $this->post_id, $this->option_1_id, $this->option_2_id );
	}

	// To implement TCP_IDetailSource
	function get_name() {
		return $this->getTitle();
	}

	function getCount() {
		return $this->count;
	}

	//Rename of getCount()
	function getUnits() {
		return $this->getCount();
	}

	// To implement TCP_IDetailSource
	function get_qty_ordered() {
		return $this->getCount();	
	}

	function setCount( $count ) {
		$this->count = $count;
	}

	function setUnits( $count ) {
		$this->setCount( $count );
	}

	function getUnitPrice() {
		return apply_filters( 'tcp_item_get_unit_price', $this->unit_price, $this->getPostId() );
	}

	// To implement TCP_IDetailSource
	function get_price() {
		return $this->getUnitPrice();
	}

	public function get_original_price() {
		return tcp_get_the_price_to_show( $this->getPostId );
	}

	function setUnitPrice( $unit_price ) {
		$this->unit_price = $unit_price;
	}

	function getTax() {
		if ( $this->tax === false ) {
			return apply_filters( 'tcp_item_get_tax', tcp_get_the_tax( $this->getPostId() ), $this->getPostId() );
		} else {
			return $this->tax;
		}
	}

	// To implement TCP_IDetailSource
	function get_tax() {
		return $this->getTax();
	}

	function setTax( $tax = false ) {
		$this->tax = $tax;
	}

	function getSKU() {
		if ( $this->sku === false ) {
			return tcp_get_the_sku( $this->post_id, $this->option_1_id, $this->option_2_id );
		} else {
			return $this->sku;
		}
	}

	// To implement TCP_IDetailSource
	function get_sku() {
		return $this->getSKU();
	}

	function setSku( $sku ) {
		$this->sku = $sku;
	}

	function getUnitWeight() {
		return apply_filters( 'tcp_item_get_unit_weight', $this->unit_weight, $this->getPostId() );
	}

	function getPriceToShow() {
		return (double)apply_filters( 'tcp_item_get_price_to_show', tcp_get_the_price_to_show( $this->getPostId(), $this->getUnitPrice() ) );
	}

	function getTotal() {
		$decimals = tcp_get_decimal_currency();	
		$total = $this->getUnitPrice() * ( 1 + $this->getTax() / 100 );
		$total = round( $total,  $decimals );
		$total = $total * $this->getCount() - $this->getDiscount();
		$total = apply_filters( 'tcp_shopping_cart_get_item_total', $total, $this->getPostId() );
		return $total;
	}

	function getTotalToShow() {
		$total = ( $this->getPriceToShow() * $this->getCount() ) - $this->getDiscount();
		$total = (float)apply_filters( 'tcp_shopping_cart_get_item_total_to_show', $total, $this->getPostId() );
		return $total;
	}

	function getWeight() {
		$weight = $this->getUnitWeight() * $this->count;
		return apply_filters( 'tcp_shopping_cart_get_weight', $weight, $this->getPostId() );
	}

	// To implement TCP_IDetailSource
	function get_weight() {
		return $this->getWeight();
	}
	function isDownloadable() {
		return $this->is_downloadable;
	}

	function setDownloadable( $is_downloadable = true ) {
		$this->is_downloadable = $is_downloadable;
	}

	function setDiscount( $discount ) {
		$this->discount = $discount;
	}

	function addDiscount( $discount ) {
		$decimals = tcp_get_decimal_currency();	
		$discount = round( $discount, $decimals );
		$this->discount += $discount;
	}

	function getDiscount() {
		$discount = $this->discount;
		return apply_filters( 'tcp_item_get_discount', $discount, $this->getPostId() );
	}

	// To implement TCP_IDetailSource
	function get_discount() {
		return $this->getDiscount();
	}

	function setFreeShipping( $free_shipping = true ) {
		$this->free_shipping = $free_shipping;
	}

	function isFreeShipping() {
		return $this->free_shipping;
	}

	function set_attributes( $attributes ) {
		$this->attributes = $attributes;
	}

	/**
	 * Adds attributes to the current list of attributes
	 *
	 * @since 1.3.6
	 */
	function add_attributes( $attributes ) {
		foreach( $attributes as $id => $attribute ) {
			$this->add_attribute( $id, $attribute );
		}
	}

	function get_attributes() {
		return $this->attributes;
	}

	function has_attributes() {
		return count( $this->attributes ) > 0;
	}
	
	function remove_attributes() {
		unset( $this->attributes );
		$this->attributes = array();
	}

	function add_attribute( $id, $value ) {
		$this->attributes[$id] = $value;
	}

	function get_attribute( $id, $default = false ) {
		return isset( $this->attributes[$id] ) ? $this->attributes[$id] : $default;
	}

	function set_attribute( $id, $value ) {
		$this->add_attribute( $id, $value );
	}

	function remove_attribute( $id ) {
		if ( isset( $this->attributes[$id] ) ) {
			unset( $this->attributes[$id] );
		}
	}

}

class ShoppingCartOtherCost {
	private $cost;
	private $desc;
	private $order;
	private $cost_to_show;

	function __construct( $cost = 0, $desc = '', $order = 0, $cost_to_show = 0 ) {
		$decimals			= tcp_get_decimal_currency();
		$this->cost 		= round( $cost, $decimals );
		$this->desc			= $desc;
		$this->order		= $order;
		$this->cost_to_show	= round( $cost_to_show, $decimals );
	}
	
	function getCost() {
		return $this->cost;
	}

	function getDesc() {
		return $this->desc;
	}

	function getOrder() {
		return $this->order;
	}

	function getCostToShow() {
		return $this->cost_to_show;
	}

	function __toString() {
		return $this->order . $this->desc;
	}
}

class ShoppingCartDiscount {
	private $discount;
	private $desc;

	function __construct( $discount, $desc = '' ) {
		$decimals		= tcp_get_decimal_currency();
		$this->discount	= round( $discount, $decimals );
		$this->desc		= $desc;
	}

	function getDiscount() {
		return $this->discount;
	}

	function getDesc() {
		return $this->desc;
	}

	function __toString() {
		return $this->desc . ': ' . $this->discount;
	}
}
endif; // class_exists check