<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    try{
        if(!empty($postedToken)){
            if(isTokenValid($postedToken)){
                //todo delete
                if (isset($_POST["delete"])) {
                    $messageID = htmlspecialchars($_POST["message_id"]);

                    deleteMessage($messageID) or throw new Exception("Couldn't delete feedback");
                    makeToast("success", "Feedback successfully deleted!", "Success");
                }
            }
            else{
                makeToast("warning", "Please refrain from attempting to resubmit previous form", "Warning");
            }
        }
        else {
            throw new exception("Token not found");
        }
    }
    catch (exception $e){
        makeToast("error", $e->getMessage(), "Error");
    }

    header("Location: /admin/manage-feedbacks.php");
    die();
}
displayToast();

$messages = retrieveAllMessage();

$token = getToken();
?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <title><?= config("name") ?> | Manage Feedback</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php admin_side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php admin_header_bar("Manage Feedbacks") ?>

            <div class="container">
                <div class="row mt-4 ms-3">
                    <div class="shadow-sm p-3 px-4 mb-5 bg-body rounded row gx-3">
                        <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
                            <div class="col">
                                <span class="h2"><span id="message-count"></span> feedbacks found</span>
                            </div>
                            <div class="row mt-3 table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Content</th>
                                        <th scope="col">Date</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if ($messages != null) {
                                        $count = 1;
                                        foreach ($messages as $message) {

                                            $type = strtoupper($message["message_type"]);
                                            $typeClass = strtolower($message["message_type"]);

                                            $date = date_create($message["date_created"]);
                                            $dateFormatted = date_format($date, "d M Y");
                                            echo
                                            "<tr class='align-middle' id='{$message["message_id"]}'>
                <th scope='row'>$count</th>
                <td class='fw-bold $typeClass'>{$type}</td>
                <td>{$message["message_email"]}</td>
                <td><pre style='font-family: Poppins; width: 400px; max-height: 400px;'>{$message["message_content"]}</pre></td>
                <td>{$dateFormatted}</td>
                <td class='text-center'>
                    <a type='button' data-bs-toggle='modal' data-bs-target='#deleteStatic' 
                    onclick='updateElement({$message["message_id"]}, \"delete\", \"message_id\");' class='h4'>
                    <i class='bi bi-trash'></i></a>
                </td>
            </tr>";
                                            $count++;
                                        }
                                        $count--;
                                        echo "<script>$('#message-count').html('$count')</script>";
                                    }
                                    else {
                                        echo "<tr><td colspan='8' class='text-center'>No aircraft found</td></tr>";
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- modal delete -->
                    <div class='modal fade' id='deleteStatic' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header bg-light-subtle'>
                                    <h5 class='modal-title' id='staticBackdropLabel'>Delete feedback?</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body bg-danger-subtle'>
                                    <div class="px-3">
                                        <div class="mb-1">
                                            <span class="fw-bolder">Warning</span>
                                        </div>
                                        <span class="text-black mt-3">This action cannot be reversed!<br>Proceed with caution.</span>
                                        <form id="delete" action="/admin/manage-feedbacks.php" method="post">
                                            <input type="hidden" name="message_id">
                                            <input type="hidden" name="token" value="<?= $token ?>">
                                        </form>
                                    </div>
                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' form="delete" name="delete" value="1" class='btn btn-danger'>I understand</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <?php footer(); ?>
        </main>

    </div>
</div>
<?php body_script_tag_content();?>
<script type="text/javascript" src="/assets/js/modal.js"></script>
</body>

</html>