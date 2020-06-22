<?php
session_start();
 
// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: index.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en" class='welcome'>
<head>
    <meta charset="UTF-8">
    <title>Bank System - Transfer Money</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="header">
    <a href="logout.php" class="logout" style="text-decoration: none; cursor:pointer;">Logout</a>		
  </div>

  <div class='transfer'>
    <a href="welcome.php" style="text-decoration: none; cursor:pointer;">< Back to Accounts</a>	
    <h2>Money Transfer</h2>
    <hr>

    <div class='container'>
      <?php
        include 'config.php';

        if($_SESSION['transfer_success']){
          echo "
          <div class='alert alert-success alert-dismissible fade show'>
            <button type='button' class='close' data-dismiss='alert'>&times;</button>
            <strong>Success:</strong> Transfer completed.
          </div>";  
          $_SESSION['transfer_success'] = FALSE;
        }

        if (isset($_POST['transferMoneyBtn'])) {
          // Define variables and initialize with empty values
          $from_aid = $_POST['from_aid'];
          $to_aid = $_POST['to_aid'];
          $amount = $_POST['amountToTransfer'];
          $hasWarnings = FALSE;
          
          // Check if from_aid is empty
          if(empty(trim($from_aid))){
            echo "
            <div class='alert alert-danger alert-dismissible fade show'>
              <button type='button' class='close' data-dismiss='alert'>&times;</button>
              <strong>Warning:</strong> You must choose an account to transfer from.
            </div>";
            $hasWarnings = TRUE;
          }
          
          // Check if to_aid is empty
          if(empty(trim($to_aid))){
            echo "
            <div class='alert alert-danger alert-dismissible fade show'>
              <button type='button' class='close' data-dismiss='alert'>&times;</button>
              <strong>Warning:</strong> You must choose an account to transfer to.
            </div>";
            $hasWarnings = TRUE;
          } 

          // Check if amount is empty
          if(empty(trim($amount))){
            echo "
            <div class='alert alert-danger alert-dismissible fade show'>
              <button type='button' class='close' data-dismiss='alert'>&times;</button>
              <strong>Warning:</strong> You must enter transfer amount bigger than 0.
            </div>";
            $hasWarnings = TRUE;
          } 

          // If no warnings start transfer
          if($hasWarnings == FALSE) {
            $sql = "SELECT balance FROM account WHERE aid='$from_aid' LIMIT 1";
            $result = mysqli_query($connection, $sql);
            $value = mysqli_fetch_object($result);
            $transferer_balance = $value->balance;

            // Check whether the balance is sufficent for the designated amount
            if($transferer_balance < $amount){
              echo "
              <div class='alert alert-danger alert-dismissible fade show'>
                <button type='button' class='close' data-dismiss='alert'>&times;</button>
                <strong>Error:</strong> Transfer failed. Insufficient amount in the account.
              </div>";
            } else {
              // Finish the transfer
              $sql = "SELECT balance FROM account WHERE aid='$to_aid' LIMIT 1";
              $result = mysqli_query($connection, $sql);
              $value = mysqli_fetch_object($result);
              $transferred_balance = $value->balance;

              // Update the variables
              $transferer_balance -= $amount;
              $transferred_balance += $amount;
              
              $update_query = "UPDATE account SET balance='$transferer_balance' WHERE aid='$from_aid'";  
              $update_query2 = "UPDATE account SET balance='$transferred_balance' WHERE aid='$to_aid'";

              if (mysqli_query($connection, $update_query) && mysqli_query($connection, $update_query2)) {
                $_SESSION['transfer_success'] = TRUE;  
                header("location: moneytransfer.php");        
              } else {
                echo "
                <div class='alert alert-danger alert-dismissible fade show'>
                  <button type='button' class='close' data-dismiss='alert'>&times;</button>
                  <strong>Error:</strong> Transfer failed. Please try again.
                </div>";
              }
            }
          }
        }
      ?>
    </div>

    <h3>1. Choose an account to send from</h3>
    <form method = "post" action = "<?php $_PHP_SELF ?>">
      <main>
        <table class='main'>
            <thead>
                <tr>
                  <th class='selection'></th>
                  <th>
                    Account ID
                  </th>
                  <th>
                    Branch
                  </th>
                  <th>
                    Balance
                  </th>
                  <th>
                    Open Date
                  </th>
                </tr>
            </thead>
            <tbody>
                <?php
                  include 'config.php';
                  $cid = $_SESSION['cid'];
                  $sql = "SELECT aid, branch, balance, openDate FROM (SELECT aid FROM owns WHERE cid = '$cid') AS tmp NATURAL JOIN account";
                  $result = mysqli_query($connection, $sql);

                  if($result-> num_rows > 0) {
                      while($row = $result-> fetch_assoc()) {
                      ?>
                          <tr>
                              <td>
                                <input type="radio" id=<?php echo $row['aid']?> name="from_aid" value=<?php echo $row['aid'];?>>
                              </td>
                              <td>
                                <label for=<?php echo $row['aid']?>><?php echo $row['aid']?></label>
                              <td>
                                <label for=<?php echo $row['aid']?>><?php echo $row['branch']?></label>
                              </td>
                              <td>
                                <label for=<?php echo $row['aid']?>><?php echo $row['balance']?></label>
                              </td>
                              <td>
                                <label for=<?php echo $row['aid']?>><?php echo $row['openDate']?></label>
                              </td>
                          </tr>
                      <?php
                      }
                      echo "</table>";
                  } else {
                    echo "
                    <tfoot>
                      <tr>
                        <th colspan='5'>
                          You dont have any accounts.
                        </th>
                      </tr>
                    </tfoot>";
                  }
                  $connection-> close();
              ?>
            </tbody>
        </table>
      </main>

      <h3>2. Choose an account to send to</h3>
      <main>
        <table class='main'>
            <thead>
                <tr>
                  <th class='selection'></th>
                  <th>
                    Account ID
                  </th>
                  <th>
                    Owner Name
                  </th>
                  <th>
                    Branch
                  </th>
                </tr>
            </thead>
            <tbody>
                <?php
                  include 'config.php';
                  $sql = "SELECT aid, name, branch FROM account NATURAL JOIN customer NATURAL JOIN owns";
                  $result = mysqli_query($connection, $sql);

                  if($result-> num_rows > 0) {
                      while($row = $result-> fetch_assoc()) {
                      ?>
                          <tr>
                              <td>
                                <input type="radio" id=<?php echo $row['aid']?> name="to_aid" value=<?php echo $row['aid'];?>>
                              </td>
                              <td>
                                <label for=<?php echo $row['aid']?>><?php echo $row['aid']?></label>
                              <td>
                                <label for=<?php echo $row['aid']?>><?php echo $row['name']?></label>
                              </td>
                              <td>
                                <label for=<?php echo $row['aid']?>><?php echo $row['branch']?></label>
                              </td>
                          </tr>
                      <?php
                      }
                      echo "</table>";
                  } else {
                    echo "
                    <tfoot>
                      <tr>
                        <th colspan='5'>
                          There are no accounts in the system.
                        </th>
                      </tr>
                    </tfoot>";
                  }
                  $connection-> close();
              ?>
            </tbody>
        </table>
      </main>

      <h3>3. Enter the amount to transfer</h3>
      <input type="number" name="amountToTransfer" id ='amountToTransfer' class='form-control' min="1">
      <input type='submit' name='transferMoneyBtn' class='submitTransferBtn' value='Submit'>
    </form>
  </div>
</body>
</html>