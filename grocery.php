<?php 
require("connect-db.php");
require("request-db.php");
?>

<?php   // form handling

$list_of_items = getAllItems();
// var_dump($list_of_requests);   // debug

if ($_SERVER['REQUEST_METHOD'] == 'GET')   // GET
{
    if (!empty($_GET['searchBtn'])) 
    {
        $list_of_items = searchItems($_GET['searchInput']);

    } 
    else if (!empty($_GET['clearBtn'])) 
    {
        $list_of_items = [];
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
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">  
    <link rel="stylesheet" href="grocery.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  

</head>

<body>  
    <div class="container">
        <div class="row g-3 mt-2">
            <div class="col">
                <h2>Maintenance Request</h2>
            </div>  
        </div>

        <form method="get" action="<?php $_SERVER['PHP_SELF'] ?>" onsubmit="return validateInput()">
            <table style="width:98%">
                <tr>
                    <td width="20%">   
                        <div class='mb-3'>
                            Search from:
                            <select class='form-select' id='priority_option' name='priority_option'>
                                <option selected></option>
                                <option value='item' >
                                    Item - search from all items in record</option>
                            </select>
                        </div>
                    </td>
                    <td width="80%">
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
            <thead>
                <tr style="background-color:#B0B0B0">
                    <th width="10%"><b>ItemID</b></th>
                    <th width="20%"><b>Name</b></th> 
                    <th width="30%"><b>Description</b></th>        
                    <th width="10%"><b>Brand</b></th>
                    <th width="20%"><b>Item Category</b></th>        
                    <!-- <th><b>Update?</b></th>
                    <th><b>Delete?</b></th> -->
                </tr>
            </thead>

            <?php if (empty($list_of_items)): ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No results found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($list_of_items as $item_info): ?>
                    <tr>
                        <td><?php echo $item_info['item_ID']; ?></td>
                        <td><?php echo $item_info['name']; ?></td>
                        <td><?php echo $item_info['description']; ?></td>
                        <td><?php echo $item_info['brand']; ?></td>
                        <td><?php echo $item_info['item_category']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

        </table>
    </div>   



</body>

</html>