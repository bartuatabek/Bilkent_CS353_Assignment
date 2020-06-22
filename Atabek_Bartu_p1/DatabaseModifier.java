import java.sql.Connection;
import java.sql.Date;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.text.SimpleDateFormat;
import static java.sql.DriverManager.getConnection;

public class DatabaseModifier {

    // JDBC driver name and database URL
    private static final String JDBC_DRIVER = "com.mysql.cj.jdbc.Driver";
    private static final String HOST = "jdbc:mysql://dijkstra.ug.bcc.bilkent.edu.tr/bartu_atabek";

    // Database credentials
    private static final String USERNAME = "bartu.atabek";
    private static final String PASSWORD = "6LBgstuF";

    // Date format
    private static final SimpleDateFormat dateFormat = new SimpleDateFormat("dd.MM.yyyy");

    public static void main(String args[]){

        Connection conn;
        Statement stmt;

        try {
            // Register JDBC driver
            Class.forName(JDBC_DRIVER);

            // Open a connection
            conn = getConnection(HOST, USERNAME, PASSWORD);
            stmt = conn.createStatement();

            // Check whether the database and/or tables exist.
            // If they exist, drop them and then re-create them.
            stmt.executeUpdate("DROP TABLE IF EXISTS owns");
            stmt.executeUpdate("DROP TABLE IF EXISTS customer");
            stmt.executeUpdate("DROP TABLE IF EXISTS account");

            // Create customer table
            // customer(cid: CHAR(12), name: VARCHAR(50), bdate: DATE, address: VARCHAR(50),
            // city: VARCHAR(20), nationality: VARCHAR(20))
            stmt.executeUpdate( "CREATE TABLE customer(" +
                    "cid CHAR(12)," +
                    "name VARCHAR(50)," +
                    "bdate DATE," +
                    "address VARCHAR(50)," +
                    "city VARCHAR(20)," +
                    "nationality VARCHAR(20)," +
                    "PRIMARY KEY(cid)) ENGINE=innodb;");

            // Create account table
            // account(aid: CHAR(8), branch: VARCHAR(20), balance: FLOAT, openDate: DATE)
            stmt.executeUpdate("CREATE TABLE account" +
                    "(aid CHAR(8)," +
                    "branch VARCHAR(20)," +
                    "balance FLOAT," +
                    "openDate DATE," +
                    "PRIMARY KEY(aid)) ENGINE=innodb;");

            // Create owns table
            // owns(cid: CHAR(12), aid: CHAR(8))
            stmt.executeUpdate("CREATE TABLE owns" +
                    "(cid CHAR(12)," +
                    "aid CHAR(8)," +
                    "PRIMARY KEY(cid, aid)," +
                    "FOREIGN KEY (cid) REFERENCES customer(cid), "
                    + "FOREIGN KEY (aid) REFERENCES account(aid))" + "ENGINE=innodb;");

            // populate the tables with data
            stmt.executeUpdate("INSERT INTO customer VALUES ('20000001','Cem', '1980-10-10' ,'Tunali', 'Ankara', 'TC');");
            stmt.executeUpdate("INSERT INTO customer VALUES ('20000002','Asli', '1985-09-08' ,'Nisantasi', 'Istanbul', 'TC');");
            stmt.executeUpdate("INSERT INTO customer VALUES ('20000003','Ahmet', '1995-02-11' ,'Karsiyaka', 'Izmir', 'TC');");
            stmt.executeUpdate("INSERT INTO customer VALUES ('20000004','John', '1990-04-16' ,'Kizilay', 'Ankara', 'ABD');");

            stmt.executeUpdate("INSERT INTO account VALUES ('A0000001', 'Kizilay', 2000.00, '2009-01-01');");
            stmt.executeUpdate("INSERT INTO account VALUES ('A0000002', 'Bilkent', 8000.00, '2011-01-01');");
            stmt.executeUpdate("INSERT INTO account VALUES ('A0000003', 'Cankaya', 4000.00, '2012-01-01');");
            stmt.executeUpdate("INSERT INTO account VALUES ('A0000004', 'Sincan', 1000.00, '2012-01-01');");
            stmt.executeUpdate("INSERT INTO account VALUES ('A0000005', 'Tandogan', 3000.00, '2013-01-01');");
            stmt.executeUpdate("INSERT INTO account VALUES ('A0000006', 'Eryaman', 5000.00, '2015-01-01');");
            stmt.executeUpdate("INSERT INTO account VALUES ('A0000007', 'Umitkoy', 6000.00, '2017-01-01');");

            stmt.executeUpdate("INSERT INTO owns VALUES ('20000001', 'A0000001');");
            stmt.executeUpdate("INSERT INTO owns VALUES ('20000001', 'A0000002');");
            stmt.executeUpdate("INSERT INTO owns VALUES ('20000001', 'A0000003');");
            stmt.executeUpdate("INSERT INTO owns VALUES ('20000001', 'A0000004');");
            stmt.executeUpdate("INSERT INTO owns VALUES ('20000002', 'A0000002');");
            stmt.executeUpdate("INSERT INTO owns VALUES ('20000002', 'A0000003');");
            stmt.executeUpdate("INSERT INTO owns VALUES ('20000002', 'A0000005');");
            stmt.executeUpdate("INSERT INTO owns VALUES ('20000003', 'A0000006');");
            stmt.executeUpdate("INSERT INTO owns VALUES ('20000003', 'A0000007');");
            stmt.executeUpdate("INSERT INTO owns VALUES ('20000004', 'A0000006');");

            // Print the contents of each table
            displayCustomers(stmt);
            displayAccounts(stmt);
            displayOwns(stmt);

            stmt.close();
            conn.close();
        } catch (ClassNotFoundException e) {
            System.out.println("Could not connect to the jdbc driver " + e);
        } catch (SQLException e) {
            System.out.println("Could not query the file " + e);
        }
    }

    private static void displayCustomers(Statement stmt) throws SQLException {
        ResultSet customers = stmt.executeQuery("SELECT * FROM customer;");
        String format = "| %-9s | %-6s | %-11s | %-9s | %-8s | %-11s |%n";
        System.out.println("\nPrinting CUSTOMER table");
        System.out.println("+-----------+--------+-------------+-----------+----------+-------------+\n" +
                           "|    cid    |  name  |    bdate    |  address  |   city   | nationality |\n" +
                           "+-----------+--------+-------------+-----------+----------+-------------+");
        while (customers.next()) {
            String cid  = customers.getString("cid");
            String name  = customers.getString("name");
            Date bdate  = customers.getDate( "bdate");
            String bdateString = dateFormat.format(bdate);
            String address  = customers.getString("address");
            String city  = customers.getString("city");
            String nationality  = customers.getString("nationality");

            System.out.printf(format, cid, name, bdateString, address, city, nationality);
            System.out.println("+-----------+--------+-------------+-----------+----------+-------------+");
        }
        customers.close();
    }

    private static void displayAccounts(Statement stmt) throws SQLException {
        ResultSet accounts = stmt.executeQuery("SELECT * FROM account;");
        String format = "| %-9s | %-8s | %-9s | %-10s |%n";
        System.out.println("\nPrinting ACCOUNT table");
        System.out.println("+-----------+----------+-----------+------------+\n" +
                           "|    aid    |  branch  |  balance  |  openDate  |\n" +
                           "+-----------+----------+-----------+------------+");
        while (accounts.next()) {
            String aid  = accounts.getString("aid");
            String branch  = accounts.getString("branch");
            Float balance = accounts.getFloat("balance");
            Date openDate  = accounts.getDate( "openDate");
            String openDateString = dateFormat.format(openDate);

            System.out.printf(format, aid, branch, balance, openDateString);
            System.out.println("+-----------+----------+-----------+------------+");
        }
        accounts.close();
    }

    private static void displayOwns(Statement stmt) throws SQLException {
        ResultSet owns = stmt.executeQuery("SELECT * FROM owns;");
        String format = "| %-9s | %-9s |%n";
        System.out.println("\nPrinting OWNS table");
        System.out.println("+-----------+-----------+\n" +
                           "|    cid    |    aid    |\n" +
                           "+-----------+-----------+");
        while (owns.next()) {
            String cid  = owns.getString("cid");
            String aid  = owns.getString("aid");

            System.out.printf(format, cid, aid);
            System.out.println("+-----------+-----------+");
        }
        owns.close();
    }
}  