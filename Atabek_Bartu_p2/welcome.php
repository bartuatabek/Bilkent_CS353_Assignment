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
    <title>Bank System - Accounts</title>
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
  <h6>Hi, <b><?php echo $_SESSION['username'];?></b>. Welcome to the Bank System.</h6>
  <h2>Accounts</h2>
  <hr>

  <div class='container'>
    <?php
        if($_SESSION['delete_success']){
          echo "
          <div class='alert alert-success alert-dismissible fade show'>
            <button type='button' class='close' data-dismiss='alert'>&times;</button>
            <strong>Success:</strong> Account deleted.
          </div>";  
          $_SESSION['delete_success'] = FALSE;
        }
    ?>
  </div>

	<main>
    <div class='container'>
      <?php
        include 'config.php';

        if (isset($_POST['deleteAccountBtn'])) {
          $aid = $_POST['aidToDelete'];

          $query = "SELECT * FROM account WHERE aid='$aid'";
          $check = mysqli_query($connection, $query);
          if (mysqli_num_rows($check) > 0){
            $del_query = "DELETE FROM owns WHERE aid='$aid'";  
            $del_query2 = "DELETE FROM account WHERE aid='$aid'";
            mysqli_query($connection, $del_query);
            mysqli_query($connection, $del_query2);
            
            $_SESSION['delete_success'] = TRUE;  
            header("location: welcome.php");
          } else { 
            echo "
            <div class='alert alert-danger alert-dismissible fade show'>
              <button type='button' class='close' data-dismiss='alert'>&times;</button>
              <strong>Error:</strong> Transfer failed. Please try again.
            </div>";
          }
          exit;
        }
      ?>
    </div>

    <table>
        <thead>
            <tr>
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
                <th></th>
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
                          <form method = "post" action = "<?php $_PHP_SELF ?>">
                            <td><?php echo $row['aid']?></td>
                            <td><?php echo $row['branch']?></td>
                            <td><?php echo $row['balance']?></td>
                            <td><?php echo $row['openDate']?></td>
                            <td class='select'>
                                <input type="hidden" name="aidToDelete" value=<?php echo $row['aid'];?>>
                                <input type='submit' name='deleteAccountBtn' class='deleteAccountBtn' value='Close'>
                            </td>
                          </form>
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
  <div class='transfer'>
    <h2>Money Transfer</h2>
    <hr>
    <a href="moneytransfer.php" class="transferMoneyBtn" style="text-decoration: none; cursor:pointer;">Transfer Money</a>		
  </div>
</body>
</html>