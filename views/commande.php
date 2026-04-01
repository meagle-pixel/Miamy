
    <!-- Common Banner Area -->
    <section id="common_banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="common_bannner_text">
                        <h2>Cart view</h2>
                        <ul>
                            <li><a href="index.html">Home</a></li>
                            <li><span><i class="fas fa-circle"></i></span>Cart view</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cart-Area -->
    <section id="cart_area_one" class="section_padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 ">
                    <div class="table_desc">
                        <div class="table_page table-responsive">
                            <table>
                                <!-- Start Cart Table Head -->
                                <thead>
                                    <tr>
                                        <th class="food_remove">Remove</th>
                                        <th class="food_thumb">Image</th>
                                        <th class="food_name">Food</th>
                                        <th class="food-price">Price</th>
                                        <th class="food_quantity">Quantity</th>
                                        <th class="food_total">Total</th>
                                    </tr>
                                </thead> <!-- End Cart Table Head -->
                                <tbody>
                                    <!-- Start Cart Single Item-->
                                    <tr>
                                        <td class="food_remove"><a href="#"><i class="far fa-trash-alt"></i></a>
                                        </td>
                                        <td class="food_thumb">
                                            <a href="food-details.html">
                                                <img src="assets/img/tab-img/item7.png" alt="img"></a>
                                        </td>
                                        <td class="food_name">
                                            <a href="food-details.html">Full Chicken Grill</a>
                                        </td>
                                        <td class="food-price">$45.00</td>
                                        <td class="food_quantity">
                                            <label>Quantity</label>
                                            <input min="1" max="100" value="1" type="number">
                                        </td>
                                        <td class="food_total">$45.00</td>
                                    </tr> <!-- End Cart Single Item-->
                                    <!-- Start Cart Single Item-->
                                    <tr>
                                        <td class="food_remove"><a href="#"><i class="far fa-trash-alt"></i></a>
                                        </td>
                                        <td class="food_thumb">
                                            <a href="food-details.html">
                                                <img src="assets/img/tab-img/item4.png" alt="igm">
                                            </a>
                                        </td>
                                        <td class="food_name">
                                            <a href="food-details.html">Noodles With Cheese</a>
                                        </td>
                                        <td class="food-price">$60.00</td>
                                        <td class="food_quantity">
                                            <label>Quantity</label>
                                            <input min="1" max="100" value="1" type="number">
                                        </td>
                                        <td class="food_total">$60.00</td>
                                    </tr> <!-- End Cart Single Item-->
                                    <!-- Start Cart Single Item-->
                                    <tr>
                                        <td class="food_remove"><a href="#"><i class="far fa-trash-alt"></i></a>
                                        </td>
                                        <td class="food_thumb">
                                            <a href="food-details.html">
                                                <img src="assets/img/tab-img/item1.png" alt="img">
                                            </a>
                                        </td>
                                        <td class="food_name">
                                            <a href="food-details.html">Double Cheese Burger</a>
                                        </td>
                                        <td class="food-price">$59.00</td>
                                        <td class="food_quantity">
                                            <label>Quantity</label>
                                            <input min="1" max="100" value="1" type="number">
                                        </td>
                                        <td class="food_total">$59.00</td>
                                    </tr> <!-- End Cart Single Item-->
                                </tbody>
                            </table>
                        </div>
                        <div class="cart_submit">
                            <button class="btn btn_theme btn_sm" type="submit">update cart</button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="order_details_right_sidebar_wrapper">
                        <div class="order_detail_right_sidebar">
                            <div class="order_details_right_boxed">
                                <div class="order_details_right_box_heading">
                                    <h3>Coupon code</h3>
                                </div>
                                <div class="coupon_code_area_order">
                                    <form action="#!">
                                        <div class="form-group">
                                            <input type="text" class="form-control bg_input"
                                                placeholder="Enter coupon code">
                                        </div>
                                        <div class="coupon_code_submit">
                                            <button class="btn btn_theme btn_sm">Apply voucher</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="order_details_right_sidebar_wrapper">
                        <div class="order_detail_right_sidebar">
                            <div class="order_details_right_boxed">
                                <div class="order_details_right_box_heading">
                                    <h3>Cart Total</h3>
                                </div>
                                <div class="order_order_amount_area">
                                    <div class="order_bokking_subtotal_area">
                                        <h6>Subtotal <span>$164</span></h6>
                                    </div>
                                    <div class="coupon_add_area">
                                        <h6><span class="remove_coupon_order">Remove</span> Coupon code (OFF 30)
                                            <span>$30.00</span>
                                        </h6>
                                    </div>
                                    <div class="total_subtotal_order">
                                        <h6>Total Amount <span>$134.00</span> </h6>
                                    </div>
                                </div>
                                <br>
                                <a href="checkout.html" class="btn btn_theme btn_sm">Proceed to Checkout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
