<?php include 'header.php'; ?>

<?php   // form handling

if (!$_SESSION["user_id"]) {
    header("Location: login.php");
    exit;
}

// echo $_SESSION["user_id"];

$results = getChangeRequests();
$results_unprocessed = $results["unprocessed"];
$results_processed = $results["processed"];
$update_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')   // GET
{
    if (!empty($_POST['submitBtn']))    // $_GET['....']
    {
        if ($_POST['updateType'] == 'addStore') {
            adminAddStore(
                $_POST['storeName'], $_POST['streetNumber'], 
                $_POST['streetName'], $_POST['city'], 
                $_POST['state'], $_POST['zipCode']
            );
        } else if ($_POST['updateType'] == 'removeStore') {
            adminRemoveStore($_POST['storeSearch']);
        } else if ($_POST['updateType'] == 'addStoreItem') {
            adminAddStoreItem(
                $_POST['storeSearch2'], $_POST['itemSearch'],
                $_POST['price'], $_POST['weight'], 
                $_POST['unit']
            );
        } else if ($_POST['updateType'] == 'changePrice') {
            adminChangePrice(
                $_POST['storeSearch2'], $_POST['itemSearch'], 
                $_POST['newPrice'], $_POST['newWeight'], 
                $_POST['newUnit']
            );
        }

        header("Location: admin_change_success.php");
    }
    else if (!empty($_POST['updateBtn'])) {
        $isChecked = isset($_POST["my_checkbox"]) ? true : false;
        if ($isChecked) {
            echo "set to rejected";
            acceptChangeRequest($_POST["rowRequestID"]);
        } else {
            rejectChangeRequest($_POST["rowRequestID"]);
        }
        header("Location: admin.php");
    }
    else if (!empty($_POST['updateBtn2'])) {
        $isChecked = isset($_POST["my_checkbox2"]) ? true : false;
        if ($isChecked) {
            echo "set to rejected";
            acceptChangeRequest($_POST["rowRequestID2"]);
        } else {
            echo "set to accepted";
            rejectChangeRequest($_POST["rowRequestID2"]);
        }
        header("Location: admin.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .search-container {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .search-input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .store-list, .item-list{
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            background-color: #fff;
            border: 1px solid #ccc;
            border-top: none;
            border-radius: 0 0 4px 4px;
            display: none;
        }

        .store-list div, .item-list div{
            padding: 8px;
            cursor: pointer;
        }

        .store-list div:hover, .item-list div:hover{
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>  
    <div class="container">
        <div class="row g-3 mt-2">
            <div class="col">
                <h2>Changes to Database</h2>
            </div>
        </div>

        <!---------------->
        <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" onsubmit="return validateForm()">
            <table style="width:98%">
                <tr id='request-type-row'>
                    <td colspan=3>
                        <div class='mb-3'>
                            Update Type:
                            <select class='form-select' id='updateType' name='updateType' onchange="updateFormFields()">
                                <option value='' <?php if ($update_type == '') echo 'selected'; ?>>
                                </option>
                                <option value='addStore' <?php if ($update_type == 'addStore') echo 'selected'; ?>>
                                    Add a store location
                                </option>
                                <option value='removeStore' <?php if ($update_type == 'removeStore') echo 'selected'; ?>>
                                    Remove a store location
                                </option>
                                <option value='addItem' <?php if ($update_type == 'addItem') echo 'selected'; ?>>
                                    Add an item
                                </option>
                                <option value='addStoreItem' <?php if ($update_type == 'addStoreItem') echo 'selected'; ?>>
                                    Add item (specific store)
                                </option>
                                <option value='changePrice' <?php if ($update_type == 'changePrice') echo 'selected'; ?>>
                                    Change price of item (specific store)
                                </option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr id='addStore1' class='hidden'>
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
                <tr id='addStore2' class='hidden'>
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
                <tr id='removeStore' class='hidden'>
                    <td>
                        <div class='mb-3' width="100%">
                            Select Store:
                            <div class="search-container">
                                <input width="100%" type="text" class="search-input form-input" name="storeSearch" id="storeSearch" placeholder="Search for a store" onkeyup="filterStores('storeSearch', 'storeList')">
                                <div class="store-list" id="storeList">
                                    <?php
                                    // Retrieve the list of stores from the database
                                    $stores = getStores();
                                    
                                    foreach ($stores as $store) {
                                        echo "<div onclick=\"selectStore('storeSearch', 'storeList', '" . $store['store_ID'] . "', '" . $store['name'] . "')\">" . $store['store_ID'] . ": " . $store['name'] . "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr id='addItem1' class='hidden'>
                    <td>
                        <div class='mb-3'>
                            Item Name:
                            <input type='text' class='form-control' id='newItemName' name='newItemName'
                                value="" />
                        </div>
                    </td>
                    <td>
                        <div class='mb-3'>
                            Item Brand:
                            <input type='text' class='form-control' id='newItemBrand' name='newItemBrand'
                                value="" />
                        </div>
                    </td>
                    <td>
                        <div class='mb-3'>
                            Item Category:
                            <input type='text' class='form-control' id='newItemCategory' name='newItemCategory'
                                value="" />
                        </div>
                    </td>
                </tr>
                <tr id='addItem2' class='hidden'>
                    <td>
                        <div class="mb-3">
                            Other Notes:
                            <textarea class='form-control' id='newItemDescription' name='newItemDescription' rows='4'></textarea>
                        </div>
                    </td>
                </tr>
                <tr id='storeItem' class='hidden'>
                    <td width="33%">
                        <div class='mb-3' width="100%">
                            Select Store:
                            <div class="search-container">
                                <input width="100%" type="text" class="search-input form-input" name="storeSearch2" id="storeSearch2" placeholder="Search for a store" onkeyup="filterStores('storeSearch2', 'storeList2')">
                                <div class="store-list" id="storeList2">
                                    <?php
                                    // Retrieve the list of stores from the database
                                    $stores = getStores();
                                    
                                    foreach ($stores as $store) {
                                        echo "<div onclick=\"selectStore('storeSearch2', 'storeList2', '" . $store['store_ID'] . "', '" . $store['name'] . "')\">" . $store['store_ID'] . ": " . $store['name'] . "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td width="33%">
                        <div class='mb-3' width="100%">
                            Select Item:
                            <div class="search-container">
                                <input width="100%" type="text" class="search-input form-input" name="itemSearch" id="itemSearch" placeholder="Search for a item" onkeyup="filterItems('itemSearch', 'itemList')">
                                <div class="item-list" id="itemList">
                                    <?php
                                    // Retrieve the list of stores from the database
                                    $items = getItems();
                                    
                                    foreach ($items as $item) {
                                        echo "<div onclick=\"selectItem('itemSearch', 'itemList', '" . $item['item_ID'] . "', '" . $item['name'] . "')\">" . $item['item_ID'] . ": " . $item['name'] . "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr id='addStoreItem' class='hidden'>
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
                            Unit (e.g. kg):
                            <input type='text' class='form-control' id='unit' name='unit'
                                value="" />
                        </div>
                    </td>
                </tr>
                <tr id='changePrice' class='hidden'>
                    <td>
                        <div class='mb-3'>
                            New Price ($):
                            <input type='text' class='form-control' id='newPrice' name='newPrice'
                                value="" />
                        </div>
                    </td>
                    <td>
                        <div class='mb-3'>
                            New Weight:
                            <input type='text' class='form-control' id='newWeight' name='newWeight'
                                value="" />
                        </div>
                    </td>
                    <td>
                        <div class='mb-3'>
                            New Unit (e.g. kg):
                            <input type='text' class='form-control' id='newUnit' name='newUnit'
                                value="" />
                        </div>
                    </td>
                </tr>
            </table>

            <div class="row g-3 mx-auto">
                <div class="col-4 d-grid ">
                    <input type="submit" value="Submit" id="submitBtn" name="submitBtn" class="btn btn-dark"
                        title="Submit database change" />
                </div>
                <div class="col-4 d-grid">
                    <input type="reset" value="Clear form" name="clearBtn" id="clearBtn" class="btn btn-secondary" />
                </div>
            </div>
        </form>

    </div>

    <!-- change request tables -->
    <div class="container">
        <h3>Unprocessed Change Requests</h3>
        <button id="toggleTableBtn" class="btn btn-primary mb-3">Hide Table</button>
        <div id="tableContainer">
            <div class="row justify-content-center">  
                <table class="w3-table w3-bordered w3-card-4 center" style="width:100%">

                    <thead>
                        <tr style="background-color:#B0B0B0">
                            <th width="5%"><b>RequestID</b></th>
                            <th width="5%"><b>UserID</b></th> 
                            <th width="5%"><b>StoreID</b></th>        
                            <th width="5%"><b>ItemID</b></th>
                            <th width="20%"><b>Request Time</b></th>        
                            <th width="50%"><b>Change Details</b></th>
                            <th width="10%"><b>Accepted</b></th>
                            <th width="10%"><b>Update</b></th>
                        </tr>
                    </thead>

                    <?php if (empty($results_unprocessed)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No results found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($results_unprocessed as $request): ?>
                            <tr>
                                <td><?php echo $request['request_ID']; ?></td>
                                <td><?php echo $request['user']; ?></td>
                                <td><?php echo $request['store']; ?></td>
                                <td><?php echo $request['item']; ?></td>
                                <td><?php echo $request['request_time']; ?></td>
                                <td><?php echo $request['change_details']; ?></td>
                                <form method="post">
                                    <input type="hidden" id="rowRequestID" name="rowRequestID" value=<?php echo $request['request_ID']; ?>>
                                <td>
                                    <input type="checkbox" id="my_checkbox" name="my_checkbox" value="0"
                                    <?php if ($request['accepted']) echo 'checked'; ?>>
                                </td>
                                <td><input type="submit" value="Update" id="updateBtn" name="updateBtn" 
                                class="btn btn-secondary" title="Update change request" /></td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </table>
            </div>  
        </div>  
        <h3 style="margin-top:20px;">Recently Processed Change Requests</h3>
        <button id="toggleTableBtn2" class="btn btn-primary mb-3">Hide Table</button>
        <div id="tableContainer2" style="margin-bottom:100px;">
            <div class="row justify-content-center">  
                <table class="w3-table w3-bordered w3-card-4 center" style="width:100%">

                    <thead>
                        <tr style="background-color:#B0B0B0">
                            <th width="5%"><b>RequestID</b></th>
                            <th width="5%"><b>UserID</b></th> 
                            <th width="5%"><b>StoreID</b></th>        
                            <th width="5%"><b>ItemID</b></th>
                            <th width="20%"><b>Request Time</b></th>        
                            <th width="50%"><b>Change Details</b></th>
                            <th width="10%"><b>Accepted</b></th>
                            <th width="10%"><b>Update</b></th>
                        </tr>
                    </thead>

                    <?php if (empty($results_processed)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No results found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($results_processed as $request): ?>
                            <tr>
                                <td><?php echo $request['request_ID']; ?></td>
                                <td><?php echo $request['user']; ?></td>
                                <td><?php echo $request['store']; ?></td>
                                <td><?php echo $request['item']; ?></td>
                                <td><?php echo $request['request_time']; ?></td>
                                <td><?php echo $request['change_details']; ?></td>
                                <form method="post">
                                    <input type="hidden" id="rowRequestID2" name="rowRequestID2" value=<?php echo $request['request_ID']; ?>>
                                <td>
                                    <input type="checkbox" id="my_checkbox2" name="my_checkbox2" value="0"
                                    <?php if ($request['accepted']) echo 'checked'; ?>>
                                </td>
                                <td><input type="submit" value="Update" id="updateBtn2" name="updateBtn2" 
                                class="btn btn-secondary" title="Update change request" /></td>
                                </form>                     
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </table>
            </div>  
        </div> 
    </div> 
</body>

<script>
function filterStores(storeSearch, storeList) {
    var input = document.getElementById(storeSearch);
    var filter = input.value.toUpperCase();
    var storeList = document.getElementById(storeList);
    var stores = storeList.getElementsByTagName('div');

    for (var i = 0; i < stores.length; i++) {
        var storeName = stores[i].textContent || stores[i].innerText;
        if (storeName.toUpperCase().indexOf(filter) > -1) {
            stores[i].style.display = '';
        } else {
            stores[i].style.display = 'none';
        }
    }

    storeList.style.display = 'block';
}

function filterItems(itemSearch, itemList) {
    var input = document.getElementById(itemSearch);
    var filter = input.value.toUpperCase();
    var itemList = document.getElementById(itemList);
    var items = itemList.getElementsByTagName('div');

    for (var i = 0; i < items.length; i++) {
        var itemName = items[i].textContent || items[i].innerText;
        if (itemName.toUpperCase().indexOf(filter) > -1) {
            items[i].style.display = '';
        } else {
            items[i].style.display = 'none';
        }
    }

    itemList.style.display = 'block';
}

function selectStore(storeSearch, storeList, storeID, storeName) {
    var input = document.getElementById(storeSearch);
    input.value = storeID + ": " + storeName;
    document.getElementById(storeList).style.display = 'none';
}

function selectItem(itemSearch, itemList, itemID, itemName) {
    var input = document.getElementById(itemSearch);
    input.value = itemID + ": " + itemName;
    document.getElementById(itemList).style.display = 'none';
}

document.addEventListener('click', function(event) {
    var searchContainer = document.querySelector('.search-container');
    if (!searchContainer.contains(event.target)) {
        document.getElementById('storeList').style.display = 'none';
    }
});

document.addEventListener('click', function(event) {
    var searchContainer = document.querySelector('.search-container');
    if (!searchContainer.contains(event.target)) {
        document.getElementById('storeList2').style.display = 'none';
    }
});

document.addEventListener('click', function(event) {
    var searchContainer = document.querySelector('.search-container');
    if (!searchContainer.contains(event.target)) {
        document.getElementById('itemList').style.display = 'none';
    }
});

const validationConfig = {
    addStore: ['storeName', 'streetNumber', 'streetName', 'city', 'state', 'zipCode'],
    removeStore: ['store', 'removeStoreReason'],
    addStoreItem: ['store', 'item', 'price', 'weight', 'unit'],
    changePrice: ['store', 'item', 'newPrice', 'newWeight', 'newUnit']
};

function validateForm() {
    const updateType = document.getElementById('updateType').value;
    const requiredFields = validationConfig[updateType];

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
    var formRows = document.querySelectorAll('#addStore1, #addStore2, #removeStore, #storeItem, #addStoreItem, #changePrice, #notes');
    formRows.forEach(function(row) {
        row.classList.add('hidden');
    });
}

function updateFormFields() {
    var updateType = document.getElementById('updateType').value;
    var addStoreFields1 = document.getElementById('addStore1');
    var addStoreFields2 = document.getElementById('addStore2');
    var removeStoreFields = document.getElementById('removeStore');
    var storeItemFields = document.getElementById('storeItem');
    var addStoreItemFields = document.getElementById('addStoreItem');
    var changePriceFields = document.getElementById('changePrice');
    var notesField = document.getElementById('notes');

    // Hide all fields
    hideFormFields();

    // Show fields based on the selected request type
    if (updateType === 'addStore') {
        addStoreFields1.classList.remove('hidden');
        addStoreFields2.classList.remove('hidden');
        notesField.classList.remove('hidden');
    } else if (updateType === 'removeStore') {
        removeStoreFields.classList.remove('hidden');
        notesField.classList.remove('hidden');
    } else if (updateType === 'addStoreItem') {
        storeItemFields.classList.remove('hidden');
        addStoreItemFields.classList.remove('hidden');
        notesField.classList.remove('hidden');
    } else if (updateType === 'changePrice') {
        storeItemFields.classList.remove('hidden');
        changePriceFields.classList.remove('hidden');
        notesField.classList.remove('hidden');
    }
}

document.getElementById('toggleTableBtn').addEventListener('click', function() {
    var tableContainer = document.getElementById('tableContainer');
    var toggleTableBtn = document.getElementById('toggleTableBtn');

    if (tableContainer.style.display === 'none') {
        tableContainer.style.display = 'block';
        toggleTableBtn.textContent = 'Hide Table';
    } else {
        tableContainer.style.display = 'none';
        toggleTableBtn.textContent = 'Show Table';
    }
});

document.getElementById('toggleTableBtn2').addEventListener('click', function() {
    var tableContainer = document.getElementById('tableContainer2');
    var toggleTableBtn = document.getElementById('toggleTableBtn2');

    if (tableContainer.style.display === 'none') {
        tableContainer.style.display = 'block';
        toggleTableBtn.textContent = 'Hide Table';
    } else {
        tableContainer.style.display = 'none';
        toggleTableBtn.textContent = 'Show Table';
    }
});

</script>

</html>