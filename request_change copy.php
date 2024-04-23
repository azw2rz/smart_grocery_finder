<?php include 'header.php'; ?>

<?php   // form handling

if (!$_SESSION["user_id"]) {
    header("Location: login.php");
    exit;
}

$isFormSubmitted = false;
$request_type = "";
$removeStoreReason = "";

// $list_of_requests = getAllRequests();
// // var_dump($list_of_requests);   // debug

if ($_SERVER['REQUEST_METHOD'] == 'POST')   // GET
{
    if (!empty($_POST['submitBtn']))    // $_GET['....']
    {
        if ($_POST['requestType'] == 'addStore') {
            requestChangeAddStore($_SESSION["user_id"], $_POST['storeName'], $_POST['streetNumber'], $_POST['streetName'],
                                $_POST['city'], $_POST['state'], $_POST['zipCode'], $_POST['notes']);
            $_SESSION["requestTypeSubmitted"] = 'addStore';
        }

        $isFormSubmitted = true;
        header("Location: request_change_success.php");
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
</head>
<body onload="hideFormFields()">
    <div class="container">
        <div class="row g-3 mt-2">
            <div class="col">
                <h2>Request Changes to Grocery Information</h2>
            </div>
        </div>

        <!---------------->
        <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" onsubmit="return validateForm()">
            <table style="width:98%">
                <tr id='request-type-row'>
                    <td colspan=3>
                        <div class='mb-3'>
                            Change Request Type:
                            <select class='form-select' id='requestType' name='requestType' onchange="updateFormFields()">
                                <option value='' <?php if ($request_type == '') echo 'selected'; ?>>
                                </option>
                                <option value='addStore' <?php if ($request_type == 'addStore') echo 'selected'; ?>>
                                    Add a store location
                                </option>
                                <option value='removeStore' <?php if ($request_type == 'removeStore') echo 'selected'; ?>>
                                    Remove a store location
                                </option>
                                <!-- <option value='addItem' <?php if ($request_type == 'addItem') echo 'selected'; ?>>
                                    Add item (general)
                                </option> -->
                                <option value='addStoreItem' <?php if ($request_type == 'addStoreItem') echo 'selected'; ?>>
                                    Add item (specific store)
                                </option>
                                <option value='changePrice' <?php if ($request_type == 'changePrice') echo 'selected'; ?>>
                                    Change price of item (specific store)
                                </option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr id='addStore1'>
                    <td width="33%">
                        <div class='mb-3'>
                            Store Name:
                            <input type='text' class='form-control' id='storeName' name='storeName'
                                value="" />
                        </div>
                    </td>
                    <td width="33%">
                        <div class='mb-3'>
                            Street Number:
                            <input type='text' class='form-control' id='streetNumber' name='streetNumber'
                                value="" />
                        </div>
                    </td>
                    <td width="33%">
                        <div class='mb-3'>
                            Street Name:
                            <input type='text' class='form-control' id='streetName' name='streetName'
                                value="" />
                        </div>
                    </td>
                </tr>
                <tr id='addStore2'>
                    <td width="33%">
                        <div class='mb-3'>
                            City:
                            <input type='text' class='form-control' id='city' name='city'
                                value="" />
                        </div>
                    </td>
                    <td width="33%">
                        <div class='mb-3'>
                            State:
                            <input type='text' class='form-control' id='state' name='state'
                                value="" />
                        </div>
                    </td>
                    <td width="33%">
                        <div class='mb-3'>
                            Zip Code:
                            <input type='text' class='form-control' id='zipCode' name='zipCode'
                                value="" />
                        </div>
                    </td>
                </tr>
                <tr id='removeStore'>
                    <td width="50%">
                        <div class='mb-3'>
                            Choose a store:
                            <select class='chosen-select form-control' id='store' name='store'>
                                <option value=''></option>
                                <?php
                                // Retrieve the list of stores from the database
                                $stores = getStores();
                                
                                foreach ($stores as $store) {
                                    echo "<option value='" . $store['store_ID'] . "'>" . $store['store_ID'] .": ". $store['name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class='mb-3'>
                            Reason:
                            <select class='form-select' id='removeStoreReason' name='removeStoreReason'>
                                <option selected></option>
                                <option value='moved' <?php if ($removeStoreReason == 'moved') echo 'selected' ?> >
                                    Moved</option>
                                <option value='renovating' <?php if ($removeStoreReason == 'renovating') echo 'selected' ?> >
                                    Renovating</option>
                                <option value='closed' <?php if ($removeStoreReason == 'closed') echo 'selected' ?> >
                                    Closed</option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr id='storeItem'>
                    <td width="33%">
                        <div class='mb-3'>
                            Choose a store:
                            <select class='chosen-select form-control' id='store' name='store' style="width:50%;">
                                <option value=''></option>
                                <?php
                                // Retrieve the list of stores from the database
                                $stores = getStores();
                                
                                foreach ($stores as $store) {
                                    echo "<option value='" . $store['store_ID'] . "'>" . $store['store_ID'] .": ". $store['name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </td>
                    <td width="33%">
                        <div class='mb-3'>
                            Item Name:
                            <input type='text' class='form-control' id='itemName' name='itemName'
                                value="" />
                        </div>
                    </td>
                    <td width="33%">
                        <div class='mb-3'>
                            Item Brand:
                            <input type='text' class='form-control' id='itemBrand' name='itemBrand'
                                value="" />
                        </div>
                    </td>
                </tr>
                <tr id='addStoreItem'>
                    <td>
                        <div class='mb-3'>
                            Price ($):
                            <input type='text' class='form-control' id='price' name='price'
                                value="" />
                        </div>
                    </td>
                    <td>
                        <div class='mb-3'>
                            Weight:
                            <input type='text' class='form-control' id='weight' name='weight'
                                value="" />
                        </div>
                    </td>
                    <td>
                        <div class='mb-3'>
                            Unit (lowercase letters):
                            <input type='text' class='form-control' id='unit' name='unit'
                                value="" />
                        </div>
                    </td>
                </tr>
                <tr id='changePrice'>
                    <td>
                        <div class='mb-3'>
                            New Price ($):
                            <input type='text' class='form-control' id='newPrice' name='newPrice'
                                value="" />
                        </div>
                    </td>
                    <td>
                        <div class='mb-3'>
                            Weight:
                            <input type='text' class='form-control' id='newWeight' name='newWeight'
                                value="" />
                        </div>
                    </td>
                    <td>
                        <div class='mb-3'>
                            Unit (lowercase letters):
                            <input type='text' class='form-control' id='newUnit' name='newUnit'
                                value="" />
                        </div>
                    </td>
                </tr>
                <!-- <tr>
                <input type='text' class='form-control' id='requestedDate' name='requestedDate'
                    placeholder='Format: yyyy-mm-dd' pattern="\d{4}-\d{1,2}-\d{1,2}"
                    value="" />
                </tr> -->
                <tr id='notes' style="display: none;">
                    <td colspan=3>
                        <div class="mb-3">
                            Other Notes:
                            <input type='text' class='form-control' id='notes' name='notes'
                                value="" />
                        </div>
                    </td>
                </tr>

            </table>

            <div class="row g-3 mx-auto">
                <div class="col-4 d-grid ">
                    <input type="submit" value="Submit" id="submitBtn" name="submitBtn" class="btn btn-dark"
                        title="Submit a change request" />
                </div>
                    <!-- <div class="col-4 d-grid ">
                    <input type="submit" value="Confirm update" id="cofmBtn" name="cofmBtn" class="btn btn-primary"
                        title="Update a maintenance request" />      
                    <input type="hidden" value="<?= $_POST['reqId'] ?>" name="cofm_reqId" />      
                        Why need to attach this cofm_reqId? 
                        Because of HTTP stateless property, $_POST['reqId'] is available to this request only. 
                        To carry over the reqId to the next round of form submision, need to pass a token to the next request. 

                    </div>	     -->
                <div class="col-4 d-grid">
                    <input type="reset" value="Clear form" name="clearBtn" id="clearBtn" class="btn btn-secondary" />
                </div>
            </div>
        </form>
    </div>
</body>


<script>

// for store search bar
$(document).ready(function() {
    $('.chosen-select').chosen({
        search_contains: true,
        no_results_text: "No stores found",
        allow_single_deselect: true,
        placeholder_text_single: "Select a store"
    });
});

const validationConfig = {
    addStore: ['storeName', 'streetNumber', 'streetName', 'city', 'state', 'zipCode'],
    removeStore: ['store', 'removeStoreReason'],
    addStoreItem: ['store', 'itemName', 'itemBrand', 'price', 'weight', 'unit'],
    changePrice: ['store', 'itemName', 'itemBrand', 'newPrice', 'newWeight', 'newUnit']
};

function validateForm() {
    const requestType = document.getElementById('requestType').value;
    const requiredFields = validationConfig[requestType];

    if (requiredFields) {
        for (const field of requiredFields) {
            const inputField = document.getElementById(field);
            if (inputField && inputField.value.trim() === '') {
                alert(`Please fill in the ${field} field.`);
                inputField.focus();
                return false;
            }
        }
    }

    return true;
}

function hideFormFields() {
    var requestType = document.getElementById('requestType').value;
    var addStoreFields1 = document.getElementById('addStore1');
    var addStoreFields2 = document.getElementById('addStore2');
    var removeStoreFields = document.getElementById('removeStore');
    var storeItemFields = document.getElementById('storeItem');
    var addStoreItemFields = document.getElementById('addStoreItem');
    var changePriceFields = document.getElementById('changePrice');
    var notesField = document.getElementById('notes');

    // Hide all fields by default
    addStoreFields1.style.display = 'none';
    addStoreFields2.style.display = 'none';
    removeStoreFields.style.display = 'none';
    storeItemFields.style.display = 'none';
    addStoreItemFields.style.display = 'none';
    changePriceFields.style.display = 'none';
    notesField.style.display = 'none';
}

function updateFormFields() {
    var requestType = document.getElementById('requestType').value;
    var addStoreFields1 = document.getElementById('addStore1');
    var addStoreFields2 = document.getElementById('addStore2');
    var removeStoreFields = document.getElementById('removeStore');
    var storeItemFields = document.getElementById('storeItem');
    var addStoreItemFields = document.getElementById('addStoreItem');
    var changePriceFields = document.getElementById('changePrice');
    var notesField = document.getElementById('notes');

    // Hide all fields by default
    addStoreFields1.style.display = 'none';
    addStoreFields2.style.display = 'none';
    removeStoreFields.style.display = 'none';
    storeItemFields.style.display = 'none';
    addStoreItemFields.style.display = 'none';
    changePriceFields.style.display = 'none';
    notesField.style.display = 'none';

    // Show/hide fields based on the selected request type
    if (requestType === 'addStore') {
        addStoreFields1.style.display = 'table-row';
        addStoreFields2.style.display = 'table-row';
        notesField.style.display = 'table-row';
    } else if (requestType === 'removeStore') {
        removeStoreFields.style.display = 'table-row';
        notesField.style.display = 'table-row';
    } else if (requestType === 'addStoreItem') {
        storeItemFields.style.display = 'table-row';
        addStoreItemFields.style.display = 'table-row';
        notesField.style.display = 'table-row';
    } else if (requestType === 'changePrice') {
        storeItemFields.style.display = 'table-row';
        changePriceFields.style.display = 'table-row';
        notesField.style.display = 'table-row';
    }
}
</script>

</html>