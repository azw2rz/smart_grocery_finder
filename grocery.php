<?php include 'header.php'; ?>

<?php   // form handling

if (!$_SESSION["user_id"]) {
    header("Location: login.php");
    exit;
}

// echo $_SESSION["user_id"];

$list_of_results = [];
$result_type = "";

// handles all GET requests
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if (!empty($_GET['searchBtn'])) 
    {
        // search from database
        // $_GET['searchType'] decides which database it searches from
        // $_GET['searchInput'] decides the keyword in the SQL WHERE clause
        $list_of_results = searchReqeust($_GET['searchType'], $_GET['searchInput']);
        $result_type = $_GET['searchType'];
    } 
    else if (!empty($_GET['clearBtn'])) 
    {
        // clears the search results
        $list_of_results = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Wilson Zheng">
    <meta name="description" content="Smart Grocery Finder App (CS 4750)">
    <title>Smart Grocery Finder</title>
</head>

<body>  
    <div class="container">
        <form method="get" action="<?php $_SERVER['PHP_SELF'] ?>" onsubmit="return validateInput()">
            <table style="width:98%">
                <tr>
                    <td width="30%">   
                        <div class='mb-3'>
                            Search from:
                            <select class='form-select' id='searchType' name='searchType'>
                                <option value='' <?php if ($result_type == '') echo 'selected'; ?>>
                                </option>
                                <option value='item' <?php if ($result_type == 'item') echo 'selected'; ?>>
                                    Item
                                </option>
                                <option value='store' <?php if ($result_type == 'store') echo 'selected'; ?>>
                                    Store
                                </option>
                                <option value='storeItemsID' <?php if ($result_type == 'storeItemsID') echo 'selected'; ?>>
                                    Items in ONE store (by StoreID)
                                </option>
                                <option value='itemInStores' <?php if ($result_type == 'itemInStores') echo 'selected'; ?>>
                                    ONE Item in stores (by ItemID)
                                </option>
                            </select>
                        </div>
                    </td>
                    <td width="70%">
                        <div class="mb-3">
                            Type in your keyword:
                            <input type='text' class='form-control' id='searchInput' name='searchInput'
                                value="" />
                        </div>
                    </td>
                </tr>
            </table>

            <div class="row g-3 mx-auto">    
                <div class="col-4 d-grid ">
                    <input type="submit" value="Search" id="searchBtn" name="searchBtn" class="btn btn-primary"
                        title="Search for an item" />                  
                </div>	       

                <div class="col-4 d-grid ">
                    <input type="submit" value="Clear" id="clearBtn" name="clearBtn" class="btn btn-dark"
                        title="Clear search results" />                  
                </div>	 
            </div>  
        </form> 
    </div>

    <div class="container">
        <h3>Search Results</h3>
        <div class="row justify-content-center">  
        <table class="w3-table w3-bordered w3-card-4 center" style="width:100%">

            <?php if ($result_type == ""): ?>
                <thead>
                    <tr style="background-color:#B0B0B0">
                        <th width="100%"><b>None</b></th>     
                        <!-- <th><b>Update?</b></th>
                        <th><b>Delete?</b></th> -->
                    </tr>
                </thead>
            <?php elseif ($result_type == "item"): ?>
                <thead>
                    <tr style="background-color:#B0B0B0">
                        <th width="10%"><b>ItemID</b></th>
                        <th width="20%"><b>Name</b></th> 
                        <th width="30%"><b>Description</b></th>        
                        <th width="10%"><b>Brand</b></th>
                        <th width="20%"><b>Item Category</b></th>        
                        <th width="10%"><b>Stores</b></th>
                    </tr>
                </thead>
            <?php elseif ($result_type == "store"): ?>
                <thead>
                    <tr style="background-color:#B0B0B0">
                        <th width="10%"><b>StoreID</b></th>
                        <th width="20%"><b>Name</b></th> 
                        <th width="15%"><b>Store Category</b></th>        
                        <th width="10%"><b>Street #</b></th>        
                        <th width="20%"><b>Street Name</b></th>        
                        <th width="10%"><b>City</b></th>        
                        <th width="5%"><b>State</b></th>        
                        <th width="10%"><b>ZIP Code</b></th>   
                        <th width="10%"><b>Items</b></th>
                    </tr>
                </thead>
            <?php elseif ($result_type == "storeItemsID"): ?>
                <thead>
                    <tr style="background-color:#B0B0B0">
                        <th width="5%"><b>StoreID</b></th>
                        <th width="15%"><b>Store Name</b></th> 
                        <th width="8%"><b>ZIP Code</b></th>        
                        <th width="5%"><b>ItemID</b></th>
                        <th width="15%"><b>Item Name</b></th> 
                        <th width="15%"><b>Brand</b></th> 
                        <th width="10%"><b>Price</b></th> 
                        <th width="5%"><b>Weight</b></th> 
                        <th width="5%"><b>Unit</b></th> 
                        <th width="20%"><b>Price per Unit</b></th>
                    </tr>
                </thead>
            <?php elseif ($result_type == "itemInStores"): ?>
                <thead>
                    <tr style="background-color:#B0B0B0">
                        <th width="5%"><b>ItemID</b></th>
                        <th width="15%"><b>Item Name</b></th> 
                        <th width="15%"><b>Brand</b></th> 
                        <th width="5%"><b>StoreID</b></th>
                        <th width="15%"><b>Store Name</b></th> 
                        <th width="8%"><b>ZIP Code</b></th>        
                        <th width="10%"><b>Price</b></th> 
                        <th width="5%"><b>Weight</b></th> 
                        <th width="5%"><b>Unit</b></th> 
                        <th width="20%"><b>Price per Unit</b></th>
                    </tr>
                </thead>
            <?php endif; ?>

            <?php if (empty($list_of_results)): ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No results found</td>
                </tr>
            <?php elseif ($result_type == "item"): ?>
                <?php foreach ($list_of_results as $item_info): ?>
                    <tr>
                        <td><?php echo $item_info['item_ID']; ?></td>
                        <td><?php echo $item_info['name']; ?></td>
                        <td><?php echo $item_info['description']; ?></td>
                        <td><?php echo $item_info['brand']; ?></td>
                        <td><?php echo $item_info['item_category']; ?></td>
                        <td>
                            <form method="get" action="<?php $_SERVER['PHP_SELF'] ?>">
                                <input type="hidden" name="searchType" value="itemInStores">
                                <input type="hidden" name="searchInput" value="<?php echo $item_info['item_ID']; ?>">
                                <input type="submit" value="Search" id="searchBtn" name="searchBtn" class="btn btn-primary"/>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php elseif ($result_type == "store"): ?>
                <?php foreach ($list_of_results as $store_info): ?>
                    <tr>
                        <td><?php echo $store_info['store_ID']; ?></td>
                        <td><?php echo $store_info['name']; ?></td>
                        <td><?php echo $store_info['store_category']; ?></td>
                        <td><?php echo $store_info['street_num']; ?></td>
                        <td><?php echo $store_info['street_name']; ?></td>
                        <td><?php echo $store_info['city']; ?></td>
                        <td><?php echo $store_info['state']; ?></td>
                        <td><?php echo $store_info['zipcode']; ?></td>
                        <td>
                            <form method="get" action="<?php $_SERVER['PHP_SELF'] ?>">
                                <input type="hidden" name="searchType" value="storeItemsID">
                                <input type="hidden" name="searchInput" value="<?php echo $store_info['store_ID']; ?>">
                                <input type="submit" value="Search" id="searchBtn" name="searchBtn" class="btn btn-primary"/>
                            </form>
                        </td>                        
                    </tr>
                <?php endforeach; ?>
            <?php elseif ($result_type == "storeItemsID"): ?>
                <?php foreach ($list_of_results as $store_info): ?>
                    <tr>
                        <td><?php echo $store_info['store_ID']; ?></td>
                        <td><?php echo $store_info['store_name']; ?></td>
                        <td><?php echo $store_info['zipcode']; ?></td>
                        <td><?php echo $store_info['item_ID']; ?></td>
                        <td><?php echo $store_info['item_name']; ?></td>
                        <td><?php echo $store_info['item_brand']; ?></td>
                        <td><?php echo $store_info['price']; ?></td>
                        <td><?php echo $store_info['weight']; ?></td>
                        <td><?php echo $store_info['unit']; ?></td>
                        <td><?php echo $store_info['price_per_unit']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php elseif ($result_type == "itemInStores"): ?>
                <?php foreach ($list_of_results as $store_info): ?>
                    <tr>
                        <td><?php echo $store_info['item_ID']; ?></td>
                        <td><?php echo $store_info['item_name']; ?></td>
                        <td><?php echo $store_info['item_brand']; ?></td>
                        <td><?php echo $store_info['store_ID']; ?></td>
                        <td><?php echo $store_info['store_name']; ?></td>
                        <td><?php echo $store_info['zipcode']; ?></td>
                        <td><?php echo $store_info['price']; ?></td>
                        <td><?php echo $store_info['weight']; ?></td>
                        <td><?php echo $store_info['unit']; ?></td>
                        <td><?php echo $store_info['price_per_unit']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

        </table>
    </div>   



</body>

</html>