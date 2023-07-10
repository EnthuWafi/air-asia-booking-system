<?php
session_start();
require("../includes/functions.inc.php");

displayToast();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php head_tag_content(); ?>
    <title><?= config("name") ?> | Frequently Asked Question</title>
</head>
<body>
<?php nav_bar(); ?>

<div class="container">
    <div class="row pt-5">
        <h1>Frequently Asked Questions</h1>

        <div class=" mt-5 accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                        Q1: What are the baggage allowances?
                    </button>
                </h2>
                <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        A1: The baggage allowances for your flight are as follows:<br>
                        - XSL: Extra Small (5 Kg)<br>
                        - SML: Small (10 Kg)<br>
                        - STD: Standard (20 Kg)<br>
                        - LRG: Large (30 Kg)<br>
                        - XLG: Extra Large (40 Kg)
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                        Q2: How can I cancel my bookings?
                    </button>
                </h2>
                <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        To cancel your bookings, go to "Manage My Bookings" and follow the instructions to cancel the desired booking.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading3">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                        Q3: How do I make a booking?
                    </button>
                </h2>
                <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        To make a booking, go to the "Flight Search" section on our website. Enter your travel details, select your desired flight, and follow the instructions to complete the booking process.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading4">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                        Q4: Can I change my flight date or time?
                    </button>
                </h2>
                <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Unfortunately, as of now, that isn't possible. You should contact our customer service support team if you have any inquiries relating to this!
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading5">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                        Q5: What payment methods are accepted?
                    </button>
                </h2>
                <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        We accept various payment methods, including online banking, and e-wallets.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading8">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse6" aria-expanded="false" aria-controls="faqCollapse8">
                        Q6: Can I pre-book seats for my flight?
                    </button>
                </h2>
                <div id="faqCollapse6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes, you can pre-book seats for your flight. During the booking process, you are required to select your preferred seats before checking out.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading9">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse7" aria-expanded="false" aria-controls="faqCollapse9">
                        Q7: Are pets allowed on board?
                    </button>
                </h2>
                <div id="faqCollapse7" class="accordion-collapse collapse" aria-labelledby="faqHeading9" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes, pets are allowed on board under certain conditions. Please refer to our pet policy on our website for detailed information on the requirements and restrictions.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading10">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse10" aria-expanded="false" aria-controls="faqCollapse10">
                        Q8: What should I do if my flight is delayed or canceled?
                    </button>
                </h2>
                <div id="faqCollapse10" class="accordion-collapse collapse" aria-labelledby="faqHeading10" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                         In the event of a flight delay or cancellation, our customer service team will assist you in rebooking or providing alternative arrangements. Please reach out to our customer support for further assistance.
                    </div>
                </div>
            </div>

        </div>


    </div>
</div>
<?php footer(); ?>


<?php body_script_tag_content(); ?>
<script>
    // Check URL hash and open corresponding accordion item
    const urlHash = window.location.hash;
    if (urlHash) {
        const targetCollapse = document.querySelector(urlHash);
        if (targetCollapse) {
            const accordion = targetCollapse.closest('.accordion');
            const accordionInstance = new bootstrap.Collapse(targetCollapse);
            if (accordion && accordionInstance) {
                accordionInstance.show();
                accordion.scrollIntoView({ behavior: 'smooth' });
            }
        }
    }
</script>
</body>
</html>

