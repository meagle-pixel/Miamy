<?php include('head.php'); ?>
<?php include('header.php'); ?>

    <!-- Common Banner Area -->
    <section id="common_banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="common_bannner_text">
                        <h2>Checkout</h2>
                        <ul>
                            <li><a href="index.html">Home</a></li>
                            <li><span><i class="fas fa-circle"></i></span>Checkout</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Submission Areas -->
    <section class="section_padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="food_order_form">
                        <h3 class="heading_theme">Billings Information</h3>
                        <div class="order_food_form_box">
                            <form action="!#" id="food_bookking_form_item">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control bg_input" placeholder="First name*">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control bg_input" placeholder="Last name*">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control bg_input"
                                                placeholder="Email address (Optional)">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control bg_input"
                                                placeholder="Mobile number*">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control bg_input"
                                                placeholder="Street address">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control bg_input"
                                                placeholder="Apartment, Suite, House no (optional)">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <select class="form-control form-select bg_input">
                                                <option value="1">Khulna</option>
                                                <option value="1">New York</option>
                                                <option value="1">Barisal</option>
                                                <option value="1">Nator</option>
                                                <option value="1">Joybangla</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <select class="form-control form-select bg_input">
                                                <option value="1">State</option>
                                                <option value="1">New York</option>
                                                <option value="1">Barisal</option>
                                                <option value="1">Nator</option>
                                                <option value="1">Joybangla</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <select class="form-control form-select bg_input">
                                                <option value="1">Country</option>
                                                <option value="1">New York</option>
                                                <option value="1">Barisal</option>
                                                <option value="1">Nator</option>
                                                <option value="1">Joybangla</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control bg_input"
                                                placeholder="Additional Notes">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="food_order_form">
                        <h3 class="heading_theme">Payment method</h3>
                        <div class="order_food_form_box">
                                <form action="!#" id="payment_checked">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="flexRadioDefault"
                                            id="flexRadioDefault1" value="red">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            Payment by card
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="flexRadioDefault"
                                            id="flexRadioDefault2" value="green">
                                        <label class="form-check-label" for="flexRadioDefault2">
                                            Paypal
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="flexRadioDefault"
                                            id="flexRadioDefault3" value="black">
                                        <label class="form-check-label" for="flexRadioDefault3">
                                            Payoneer
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="flexRadioDefault"
                                            id="flexRadioDefault4" value="white">
                                        <label class="form-check-label" for="flexRadioDefault4">
                                            Cash on delivery
                                        </label>
                                    </div>
                                    <div class="payment_filed_wrapper">
                                        <div class="payment_card payment_toggle red">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control bg_input"
                                                            placeholder="Card number">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control bg_input"
                                                            placeholder="Cardholder name">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control bg_input"
                                                            placeholder="Date of expiry">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control bg_input"
                                                            placeholder="Security code">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="paypal_payment payment_toggle green">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control bg_input"
                                                            placeholder="Email Address">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="payoneer_payment payment_toggle black">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control bg_input"
                                                            placeholder="Email Address">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                        </div>
                    </div>
                    <div class="food_order_form_submit">
                        <div class="form-check write_spical_check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefaultf1">
                            <label class="form-check-label" for="flexCheckDefaultf1">
                                I read and accept all <a href="terms-service.html">Terms and conditios</a>

                            </label>
                        </div>
                        <a href="order-success.html" class="btn btn_theme btn_md">Place Order</a>
                    </div>

                </div>
                <br><br>
                <div class="col-lg-4">
                    <div class="order_details_right_sidebar_wrapper">
                        <div class="order_detail_right_sidebar">
                            <div class="order_details_right_boxed">
                                <div class="order_details_right_box_heading">
                                    <h3>Your Orders</h3>
                                </div>

                                <div class="order_order_amount_area">
                                    <ul>
                                        <li>Full Chicken Grill x 1 <span>$45.00</span></li>
                                        <li>Noodles With Cheese x 1 <span>$60.00</span></li>
                                        <li>Double Cheese Burger x 1 <span>$59.00</span></li>
                                    </ul>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include('footer.php'); ?>
<?php include('foot.php'); ?>