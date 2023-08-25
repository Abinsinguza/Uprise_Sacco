
import java.io.*;
import java.net.*;
import java.sql.*;
import java.time.*;
import java.util.HashMap;
import java.util.Map;
import java.util.UUID;



public class Server {
    // Database connection parameters (change these as needed)
    private static final String DB_URL = "jdbc:mysql://localhost:3306/sacco";
    private static final String DB_USER = "root";
    private static final String DB_PASSWORD = "";
    private static Map<String, Boolean> loggedInClients = new HashMap<>();

        
    public static String addLoanrequest(String username, double amount ,int period) {
        String memberId = getUserIdByUsername(username);
       
        String notesystem = "has just requested for a loan but Loan request could not be processed by due to system failure";
        
        double loan_limit = getloan_limit(memberId);
        double netFunds = netFunds();

        // Check if there is an "in progress" loan for the member
    if (hasInProgressLoan(memberId)) {
       String note2="has just requested for a loan but Loan request could not be processed by due to having loan in progress";
             processRequestnotification( memberId,note2);

        System.out.println("You already have a loan in progress. Please complete or cancel the existing loan before requesting a new one.");
        return "You already have a loan in progress. Please complete or cancel the existing loan before requesting a new one.";
       
    }   


        // Check if the loan_limit is less than or equal to the requested amount
    if (loan_limit <= amount) {
        String note3="has just requested for a loan but Loan request could not be processed by due to exceeding the loan limit of UGX,"+ loan_limit;
        processRequestnotification( memberId,note3);

        System.out.println("Loan request exceeds the loan limit.");
        return "Your loan request amount exceeds your loan limit. Your maximum eligible loan amount is UGX," + loan_limit;
    }

      // Check if netFunds are greater than 2000000
      if (netFunds <= 2000000) {
        String note4 = "has just requested for a loan but Loan request could not be processed by due to insufficient avaliable funds for a new loan request";
        processRequestnotification( memberId,note4);
        System.out.println("Net funds are not sufficient for a new loan request.");
        return "Net funds are not sufficient for a new loan request.";
    }
    
        // Initialize the reference number to be returned
        String insertQuery = "INSERT INTO loanrequests (memberId, loan_amount, periodInMonths, created_at ) VALUES (?, ?, ?, ?)";
    
        try {
            try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement insertStmt = connection.prepareStatement(insertQuery)) {
                // Set the parameters for the insert query
                insertStmt.setString(1, memberId);
                insertStmt.setDouble(2, amount); // Corrected parameter index
                insertStmt.setInt(3, period);
                
                // Set the current timestamp for created_at field
                Timestamp currentTimestamp = Timestamp.from(Instant.now());
                insertStmt.setTimestamp(4, currentTimestamp);
    
                // Execute the insert query
                int affectedRows = insertStmt.executeUpdate();
                
                if (affectedRows == 0) {
                    // Insertion failed, handle the error (e.g., throw an exception)
                    System.out.println("Failed to insert loan details.");
                    processRequestnotification( memberId,notesystem);
                    return "Failed to insert loan details";
                } else {
                    String ref = getreferenceNo(amount, period, memberId);
                    System.out.println("Your reference number is: " + ref); // Print the correct reference number
                    String tm = Integer.toString(period);
                    String loanAmountStr = Double.toString(amount);
                     String note ="has just successfully made a loan request  Reference Number: " + ref +" of amount UGX, "+ loanAmountStr + " to be cleared in " + tm +" months";
                    processRequestnotification( memberId,note);
                    return "Loan request successfully added with reference number: " + ref; // Provide a successful message
                }
            }
        } catch (SQLException e) {
            // Handle any exceptions that may occur during the database query
            e.printStackTrace();
            return "Failed to insert loan details";
        }
    }

    public static String processRequestnotification(String memberId, String data) {
        String insertQ = "INSERT INTO notifications (memberId, data, created_at, updated_at) VALUES (?, ?, NOW(), NOW())";
    
        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD)) {
            try (PreparedStatement insertStmt = connection.prepareStatement(insertQ)) {
                // Set the parameters for the insert query
                insertStmt.setString(1, memberId);
                insertStmt.setString(2, data);
    
                // Execute the insert query
                int affectedRows = insertStmt.executeUpdate();
    
                if (affectedRows == 0) {
                    // Insertion failed, handle the error (e.g., throw an exception)
                    System.out.println("Failed to insert notification.");
                    return "Failed to insert notification";
                } else {
                    return "Notification created successfully.";
                }
            }
        } catch (SQLException e) {
            // Handle any exceptions that may occur during the database query
            e.printStackTrace();
            return "An error occurred while processing the request.";
        }
    }
    
         



    // check 
    public static boolean hasInProgressLoan(String memberId) {
        boolean hasInProgressLoan = false;
    
        String query = "SELECT COUNT(*) AS count FROM loans WHERE memberId = ? AND status = 'in progress'";
    
        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(query)) {
            statement.setString(1, memberId);
    
            try (ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    int count = resultSet.getInt("count");
                    hasInProgressLoan = count > 0;
                }
            }
        } catch (SQLException e) {
            e.printStackTrace(); // Handle or log the exception more appropriately
        }
    
        return hasInProgressLoan;
    }
    


   /// getting members loan limit
    private static double getloan_limit(String memberId) {
        double loan_limit = 0.0; // Initialize the loan_limit to 0.0
    
        // Prepare the SQL query to retrieve the balance based on the provided memberId
        String sql = "SELECT balance FROM members WHERE memberId = ?";
    
        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD)) {
            PreparedStatement preparedStatement = connection.prepareStatement(sql);
            preparedStatement.setString(1, memberId); // Set the memberId as a parameter in the query
    
            // Execute the query
            ResultSet resultSet = preparedStatement.executeQuery();
    
            // Check if the query returned a result
            if (resultSet.next()) {
                double bal = resultSet.getDouble("balance"); // Retrieve the balance from the query result
                loan_limit = 0.75 * bal;
            }
        } catch (SQLException e) {
            e.printStackTrace(); // Handle or log the exception more appropriately
        }
    
        return loan_limit;
    }
    


  /// geting net funds
    public static double netFunds() {
        double difference = 0.0;
    
        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD)) {
            String queryk = "SELECT (SELECT SUM(amount) FROM deposits) - (SELECT SUM(loanBalance) FROM loans) AS difference";
            
            try (PreparedStatement statement = connection.prepareStatement(queryk);
                 ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    difference = resultSet.getDouble("difference");
                }
            }
        } catch (SQLException e) {
            e.printStackTrace(); // Handle or log the exception more appropriately
        }
    
        return difference;
    }
    

    // get memberId
public static String getMember(String password, String username) throws SQLException {
        String dbmemberId = "";
        String sqlmember = "SELECT memberId FROM members WHERE password = ? AND username = ?";

        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(sqlmember)) {
            statement.setString(1, password);
            statement.setString(2, username);
            try (ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    dbmemberId = resultSet.getString("memberId");
                }
            }
        }

        return dbmemberId;
    }


    ///loging in method
        public static String authlogin(String username,String password) throws SQLException {
        String query = "SELECT memberId, password, name, username FROM members WHERE username = ?";
        String loginMemberId="";
        String name="";
        String dbpassword="";
        String dbusername="";
        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(query)) {
                     // Set the username parameter in the query
                     statement.setString(1, username);

                     // Execute the query
               try (ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    dbpassword= resultSet.getString("password");
                    dbusername = resultSet.getString("username");
                    name=resultSet.getString("name");
                    loginMemberId = resultSet.getString("memberId");
                   
                } 
            }
        
        } catch (SQLException e) {
            // Handle any exceptions that may occur during the database query
            e.printStackTrace();
            return "error";
        }
        
             if ( password.equals(dbpassword) && username.equals(dbusername)){
                System.out.println("login successful " + name + loginMemberId);
                return "login";
            } else if (!password.equals(dbpassword) && username.equals(dbusername)){
                return "wrong password";
            } else {
                System.out.println("receipt doesnt exist , try again later ");
                return "username not found";
            }

        
    }

     public static String getreferenceNo(double loan_amount, int period, String memberId) throws SQLException {
        String referenceNumber = null;
        String retrieveQuery = "SELECT referenceNumber  FROM loanrequests WHERE loan_amount = ? AND periodInMonths = ? AND memberId = ?";

        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(retrieveQuery)) {
            statement.setDouble(1, loan_amount);
            statement.setInt(2,period);
            statement.setString(3, memberId);
            try (ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    referenceNumber = resultSet.getString("referenceNumber");
                }
            }
        }

        return referenceNumber;
    }


    public static String processLoanRequest(double loanamount, String memberId, int period) throws SQLException {
        // Initialize the reference number to be returned
        String insertQuery = "INSERT INTO loanrequests (memberId, loan_amount, periodInMonths) VALUES (?, ?, ?)";
    
        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD)) {
            try (PreparedStatement insertStmt = connection.prepareStatement(insertQuery)) {
                // Set the parameters for the insert query
                insertStmt.setString(1, memberId);
                insertStmt.setDouble(2, loanamount);
                insertStmt.setInt(3, period);
    
                // Execute the insert query
                int affectedRows = insertStmt.executeUpdate();
    
                if (affectedRows == 0) {
                    // Insertion failed, handle the error (e.g., throw an exception)
                    System.out.println("Failed to insert loan details.");
                    return "no";
                } else {
                    return "yes";
                }
            }
        } catch (SQLException e) {
            // Handle any exceptions that may occur during the database query
            e.printStackTrace();
            return "error";
        }
    }
         
    // authenticate method
    public static String deposit(double amount, String date ,String receiptNo, String username) throws SQLException {
         String memberId = getUserIdByUsername(username);

        String query = "SELECT status FROM deposits WHERE memberId = ? AND receiptNo = ? AND amount = ? AND date = ?";
    
        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(query)) {
            statement.setString(1, memberId);
            statement.setString(2, receiptNo);
            statement.setDouble(3, amount);
            statement.setString(4, date);
    
            try (ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    String status = resultSet.getString("status");
                   // System.out.println("Receipt exists with status: " + status);
                     if ("deposited".equals(status)|| "loan payment".equals(status)) {
                        System.out.println("Receipt is already deposited");
                        return "Receipt is already deposited";
                    } else if ("pending".equals(status)) {
                        //performPendingActions(); 
                        double currentBalance = getAccountBalance(receiptNo);
                        String not = "has just made a deposit of UGX,"+ Double.toString(amount) + " making his total Contribution to UGX,"+ Double.toString(currentBalance);
                        performDeposit( amount,date,receiptNo);
                       
                        processRequestnotification(memberId,not);
                        System.out.println("Deposit successful, your account balance is UGX, "+ currentBalance);
                         return "Deposit successful, your account balance is UGX,"+ currentBalance;
                    } else {
                        System.out.println("Unknown status: " + status);
                    }
                    
                    return status;
                } else {
                    String missing = "has checked for a missing receipt of Receipt Number: "+ receiptNo + " and amount of UGX,"+ Double.toString(amount);
                    processRequestnotification(memberId,missing);
                    System.out.println("Receipt doesn't exist, try again later");
                    return "Receipt doesn't exist, try again after 24 Hours";
                }
                
            }
        } catch (SQLException e) {
            // Handle any exceptions that may occur during the database query
            e.printStackTrace();
            return "error";
        }
    }
    


    // Fetches the account balance from the database based on the receipt number
    public static double getAccountBalance(String receiptNumber) throws SQLException {
        double balance = 0.0;
        String query = "SELECT m.balance " +
               "FROM members m " +
               "INNER JOIN deposits d ON m.memberId = d.memberId " +
               "WHERE d.receiptNo= ?";

        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(query)) {
            statement.setString(1, receiptNumber);

            try (ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    balance = resultSet.getDouble("balance");
                }
            }
        }

        return balance;
    }

    // Deposits the amount into the account and updates the balance in the database
    public static boolean performDeposit(Double amount, String date, String receiptNo) {
        try {
            // Connect to the database and fetch the current balance
            double balance = getAccountBalance(receiptNo);

            // Update the balance with the deposited amount
            double newBalance = balance + amount;

            // Save the new balance to the database (you need an UPDATE query)

            // For example:
            String updateQuery = "UPDATE members m " +
                     "JOIN deposits d ON m.memberId = d.memberId " +
                     "SET m.balance = ?, d.status = 'deposited' " +
                     "WHERE d.receiptNo = ?";

            try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
                 PreparedStatement statement = connection.prepareStatement(updateQuery)) {
                statement.setDouble(1, newBalance);
                statement.setString(2, receiptNo);
                statement.executeUpdate();
            }

            // Log the successful deposit and return true
            System.out.println("Deposit successful");
            return true;
        } catch (SQLException e) {
            e.printStackTrace();
            // If there's an error, log the failure and return false
            System.out.println("Deposit not successful");
            return false;
        }
    }

    private static boolean isValidCredentials(String username, String password) {
        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD)) {
            String sql = "SELECT * FROM members WHERE username = ? AND password = ?";
            PreparedStatement statement = connection.prepareStatement(sql);
            statement.setString(1, username);
            statement.setString(2, password);

            ResultSet resultSet = statement.executeQuery();
            boolean isValid = resultSet.next();

            resultSet.close();
            statement.close();

            return isValid;
        } catch (SQLException e) {
            e.printStackTrace();
            return false;
        }
    }

    private static String getUserIdByUsername(String username) {
        String userId = ""; // Initialize the userId to an empty string
    
        // Prepare the SQL query to retrieve the userId based on the provided username
        String selectQuery = "SELECT memberId FROM members WHERE username = ?";
    
        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(selectQuery)) {
    
            statement.setString(1, username); // Set the username as a parameter in the query
    
            // Execute the query
            ResultSet resultSet = statement.executeQuery();
    
            // Check if the query returned a result
            if (resultSet.next()) {
                userId = resultSet.getString("memberId"); // Retrieve the userId from the query result
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    
        return userId;
    }

    private static void handleDepositCommand(String command, PrintWriter writer, String username) {
        String[] parts = command.split(" ");
        if (parts.length == 4) {
            double amount = Double.parseDouble(parts[1]);
            String dateDeposited = parts[2];
            String receiptNumber = parts[3];
            try {
                String resultMessage = deposit(amount, dateDeposited, receiptNumber, username);
                writer.println(resultMessage);
            } catch (SQLException e) {
                e.printStackTrace(); // Handle or log the exception more appropriately
                writer.println("An error occurred during deposit.");
            }
        } else {
            writer.println("Invalid deposit command!");
        }
    }
    
    private static void handleRequestLoanCommand(String command, PrintWriter writer, String username) {
        String[] parts = command.split(" ");
        if (parts.length == 3) {
            double amount = Double.parseDouble(parts[1]);
            String paymentPeriodInMonths = parts[2];
            int period = Integer.parseInt(paymentPeriodInMonths);
            // Process the loan request command and perform database operations
            String loanApplicationId = addLoanrequest(username, amount, period);
            if (!loanApplicationId.isEmpty()) {
                writer.println(loanApplicationId);
            } else {
                writer.println("Failed to submit loan application.");
            }
        } else {
            writer.println(
                    "Invalid requestLoan command format. Please use 'requestLoan amount paymentPeriodInMonths'.");
        }
    }
    

    private static void handleLoanRequestStatusCommand(String command, PrintWriter writer, String username) {
        String[] parts = command.split(" ");
        if (parts.length == 2) {
            String loanApplicationId = parts[1];
            // Process the loan request status command and perform database operations
           // String status = getLoanRequestStatus(loanApplicationId, username);
            String hh = getLoanRequestStatus(loanApplicationId,username);
            writer.println(hh);

        } else {
            writer.println(
                    "Invalid LoanRequestStatus command format. Please use 'LoanRequestStatus loanApplicationId'.");
        }
    }


     private static String getLoanRequestStatus(String referenceNumber, String username) {
            String memberId = getUserIdByUsername(username);


        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD)) {
      
            String selectSql = "SELECT approval FROM loanrequests WHERE referenceNumber = ? AND memberId = ?";
            PreparedStatement selectStatement = connection.prepareStatement(selectSql);
            selectStatement.setString(1, referenceNumber ); // Sevalue in the PreparedStatement
            selectStatement.setString(2, memberId );
            ResultSet resultSet = selectStatement.executeQuery();

            if (resultSet.next()) {
                String status = resultSet.getString("approval").toLowerCase(); // Convert to lowercase
                resultSet.close();
                selectStatement.close();
                connection.close();
                if ("pending".equals(status)) {
                    return"Your loan request is still pending. Please check back after 24 hours.";
                   // performPendingActions(); // Call the method for pending status
                } else if ("grant".equals(status)) {
                    return "Congratulations!" + "Your loan request has been approved."+ " To accept, enter:<ACCEPT><LOANID> To reject, enter:<REJECT><LOANID>";
                               
                   // performGrantActions(); // Call the method for grant status
                } else if ("deny".equals(status)) {
                   return "We regret to inform you that your loan request has been denied. Please contact our customer care for any inquiries.";
                  // performDenyActions(); // Call the method for deny status
                }
                
                return status;
            
            }

            resultSet.close();
            selectStatement.close();
            connection.close();
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return "Loan request not found.";
    }

// methods to check uo reference number
       public static String getref(String memberId) throws SQLException {
        String referenceNumber = null;
        String retrieveQuery = "SELECT referenceNumber FROM loanrequests WHERE approval = 'Grant' AND clientChoice = 'pending' AND memberId = ?";

        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(retrieveQuery)) {
            statement.setString(1, memberId);
            try (ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    referenceNumber = resultSet.getString("referenceNumber");
                }
            }
        }

        return referenceNumber;
    }
    
 public static String regloan( String memberId, String refNo) throws SQLException {
        // Initialize loan id to be returned
        String inQuery = "INSERT INTO loans (referenceNumber,created_at, updated_at) VALUES (?,NOW(), NOW())";
    
        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD)) {
            try (PreparedStatement insertStmt = connection.prepareStatement(inQuery)) {
                // Set the parameters for the insert query
                insertStmt.setString(1,refNo);
                
    
                // Execute the insert query
                int affectedRows = insertStmt.executeUpdate();
    
                if (affectedRows == 0) {
                    // Insertion failed, handle the error (e.g., throw an exception)
                    System.out.println("Failed to insert loan details.");
                    return null;
                } else {
                    setClient(refNo);
                   String loanId= getloanId( memberId,refNo);
                    return loanId;
                }
            }
        } catch (SQLException e) {
            // Handle any exceptions that may occur during the database query
            e.printStackTrace();
            return null;
        }
    }
      
     public static String getloanId(String memberId, String refNo) throws SQLException {
        String loanId = null;
        String retrieveQuery = "SELECT loanId  FROM loans WHERE memberId = ? AND referenceNumber = ? ";

        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(retrieveQuery)) {
            statement.setString(1, memberId);
            statement.setString(2,refNo);
           
            try (ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    loanId = resultSet.getString("loanId");
                }
            }
        }

        return loanId;
    }

     //upadating clienting choice
      public static boolean setClient(String refNo) {
        String updateQ = "UPDATE loanrequests " +
                         "SET clientChoice = 'accepted' " +
                         "WHERE referenceNumber = ? ";
    
        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(updateQ)) {
            statement.setString(1, refNo);
    
            int rowsUpdated = statement.executeUpdate();
    
            // Check the number of rows affected to determine success
            return rowsUpdated > 0;
        } catch (SQLException e) {
            e.printStackTrace();
            return false;
        }
    }
    

    
private static void handleLoanAcceptCommand(String command, PrintWriter writer, String username) {
    try {
        // Extract loan application ID from the command
        String memberId = getUserIdByUsername(username);
        String[] parts = command.split(" ");
        if (parts.length != 2) {
            writer.println("Invalid LoanAccept command format. Please use 'Accept loanapplicationid '.");
            return;
        }
        
        String loanApplicationId = parts[1];
       
        String dey = getref(memberId);
        if (dey != null) {
             String loanId =  regloan(memberId,loanApplicationId);
             String noteaccept = "has just accepted the loan granted with loan Id:" + loanId;
             processRequestnotification(memberId,noteaccept);
            writer.println("Loan has been accepted. Your loan id is " + loanId + ".");
        } else {
            writer.println("Failed to accept loan application.");
        }
    } catch (SQLException e) {
        // Handle the SQL exception here
        e.printStackTrace();
        writer.println("An error occurred while processing the loan application.");
    }
}

private static String rejectLoanApplication(String loanApplicationId ,String memberId) {
        // Implement the logic to update the loan application status to 'rejected' in the loanrequests table
        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD)) {
            String updateSql = "UPDATE loanrequests SET clientChoice = 'rejected' WHERE referenceNumber = ? AND memberId= ?";
            PreparedStatement updateStatement = connection.prepareStatement(updateSql);
            updateStatement.setString(1, loanApplicationId);
            updateStatement.setString(2, memberId);
            int rowsAffected = updateStatement.executeUpdate();
            if (rowsAffected > 0) {
                String reject = "has just rejected the loan granted with Reference No:" + loanApplicationId;
                processRequestnotification(memberId,reject);
               return ("Loan application " + loanApplicationId + " has been rejected.");
            } else {
               return ("Failed to reject loan application " + loanApplicationId + ".");
            }
        } catch (SQLException e) {
            e.printStackTrace();
            return "error";
        }
    }

              //check
            public static String getref2(String memberId, String refno) throws SQLException {
        String referenceNumber = null;
        String retrieveQuery = "SELECT referenceNumber FROM loanrequests WHERE approval = 'Grant' AND clientChoice = 'pending' AND memberId = ? AND referenceNumber= ?";

        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(retrieveQuery)) {
            statement.setString(1, memberId);
            statement.setString(2, refno);
            try (ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    referenceNumber = resultSet.getString("referenceNumber");
                }
            }
        }

        return referenceNumber;
    }


private static void handleLoanRejectCommand(String command, PrintWriter writer, String username) {

       try {
    String memberId = getUserIdByUsername(username);
    // Extract loan application ID from the command
    String[] parts = command.split(" ");
    if (parts.length != 2) {
        writer.println("Invalid LoanReject command format. Please use 'LoanReject loanapplicationid reject'.");
        return;
    }
    String loanApplicationId = parts[1];
       
        String dey2 = getref2(memberId, loanApplicationId);

    if (dey2 != null) {
        String reject  =  rejectLoanApplication(loanApplicationId , memberId);
           //  String loanId =  regloan(memberId,loanApplicationId);
            writer.println(reject);
        } else {
            writer.println("Failed to reject loan request.");
        }
    } catch (SQLException e) {
        // Handle the SQL exception here
        e.printStackTrace();
        writer.println("An error occurred while processing the loan application.");
    }
}
  
 

private static void handleLoanDepositCommand(String command, PrintWriter writer, String username) {
    String[] parts = command.split(" ");
    String memberId = getUserIdByUsername(username);
        if (parts.length == 5) {
            double amount = Double.parseDouble(parts[2]);
            String dateDeposited = parts[3];
            String receiptNumber = parts[4];
            String loanId = parts[1];
            try {
                String resMessage = payloan(amount, dateDeposited, receiptNumber, memberId,loanId);
                writer.println(resMessage);
            } catch (SQLException e) {
                e.printStackTrace(); // Handle or log the exception more appropriately
                writer.println("An error occurred during deposit.");
            }
        writer.println("Invalid lonpayment command!");
    }
}


public static String payloan(double amount, String date ,String receiptNo, String memberId ,String loanId) throws SQLException {

        String query = "SELECT status FROM deposits WHERE memberId = ? AND receiptNo = ? AND amount = ? AND date = ?";
    
        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(query)) {
            statement.setString(1, memberId);
            statement.setString(2, receiptNo);
            statement.setDouble(3, amount);
            statement.setString(4, date);
    
            try (ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    String status = resultSet.getString("status");
                   // System.out.println("Receipt exists with status: " + status);
                     if ("deposited".equals(status)) {
                        System.out.println("Receipt is already deposited");
                        return "Receipt is already deposited";
                    } else if ("pending".equals(status)) {
                        //performPendingActions();
                        boolean success = performloanpay(amount,memberId,loanId);
                        if (success) {
                            double loanBalance = getLoanBalance(memberId, loanId);
                            updateReceiptStatus(memberId,amount,date,receiptNo);
                            // Call your additional methods here
                            // For example
                             String notepayloan= "has just made a loan payment to loan:" + loanId;
                             processRequestnotification(memberId,notepayloan);
                            System.out.println("Transaction successful, your account balance is UGX," + loanBalance);
                            return "Transaction successful, your loan balance is UGX, " + loanBalance;
                        } else {
                            // Handle the case when loan payment is not successful
                            return "Loan payment not successful";
                        }
                        
                    } else if ("loan payment".equals(status)) {
                        //performPendingActions();
                        System.out.println("Receipt already deposited on your loan payment");
                         return "Receipt already deposited on your loan payment";
                    }
                    else {
                        System.out.println("Unknown status: " + status);
                    }
                    
                    return status;
                } else {
                    System.out.println("Receipt doesn't exist, try again later");
                    return "Receipt doesn't exist, try again later";
                }
                
            }
        } catch (SQLException e) {
            // Handle any exceptions that may occur during the database query
            e.printStackTrace();
            return "error";
        }
    }
     
       // clear you loan with the amount  and updates the loanbalance in the database
       public static boolean performloanpay(Double amount, String memberId, String loanId) {
        try {
            // Update the loans table by increasing the amountCleared with the specified amount
            String updateQuery = "UPDATE loans SET amountCleared = amountCleared + ? , updated_at = NOW() WHERE memberId = ? AND loanId = ? AND status ='in progress'";
    
            try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
                 PreparedStatement statement = connection.prepareStatement(updateQuery)) {
                statement.setDouble(1, amount);
                statement.setString(2, memberId);
                statement.setString(3, loanId);
                
                int rowsUpdated = statement.executeUpdate();
    
                if (rowsUpdated > 0) {
                    // Log the successful loan payment and return true
                    System.out.println("Loan payment successful");
                    return true;
                } else {
                    // Log the failure and return false
                    System.out.println("Invalid LoanId or receipt  ");
                    return false;
                }
            }
        } catch (SQLException e) {
            e.printStackTrace();
            // If there's an error, log the failure and return false
            System.out.println("Loan payment not successful");
            return false;
        }
    }
    
      // Fetches the account balance from the database based on the receipt number
    public static double getAmountCleared(String memberId, String loanId) throws SQLException {
        double amountCleared = 0.0;
        String query = "SELECT amountCleared FROM loans WHERE memberId = ? AND loanId = ?";


        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(query)) {
            statement.setString(1, memberId);
             statement.setString(2, loanId);

            try (ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    amountCleared = resultSet.getDouble("amountCleared");
                }
            }
        }

        return amountCleared;
    }

     public static double getLoanBalance(String memberId, String loanId) throws SQLException {
        double loanBalance = 0.0;
        String query = "SELECT loanBalance FROM loans WHERE memberId= ? AND loanId= ?";

        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(query)) {
            statement.setString(1, memberId);
             statement.setString(2, loanId);

            try (ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    loanBalance = resultSet.getDouble("loanBalance");
                }
            }
        }

        return loanBalance;
    }

    public static boolean updateReceiptStatus(String memberId, double amount, String date, String receiptNo) {
        try {
            String updateQuery = "UPDATE deposits SET status = 'loan payment', updated_at = NOW() WHERE memberId = ? AND amount = ? AND date = ? AND receiptNo = ?";
    
            try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
                 PreparedStatement statement = connection.prepareStatement(updateQuery)) {
                statement.setString(1, memberId);
                statement.setDouble(2, amount);
                statement.setString(3, date);
                statement.setString(4, receiptNo);
                
                int rowsUpdated = statement.executeUpdate();
    
                if (rowsUpdated > 0) {
                    // Log the successful update and return true
                    System.out.println("Receipt status updated to 'loan payment'");
                    return true;
                } else {
                    // Log the failure and return false
                    System.out.println("Failed to update receipt status");
                    return false;
                }
            }
        } catch (SQLException e) {
            e.printStackTrace();
            // If there's an error, log the failure and return false
            System.out.println("Failed to update receipt status");
            return false;
        }
    }


    private static void handleCheckStatementCommand(String command, PrintWriter writer, String username) {
        String[] parts = command.split(" ");
        if (parts.length == 3) {
            String dateFrom = parts[1];
            String dateTo = parts[2];
            // Process the check statement command and perform database operations
            String statements = checkStatement(dateFrom, dateTo, username);
            if (!statements.isEmpty()) {
               
                    writer.println("Statement Information:"+statements);
                
            } else {
                writer.println("No transactions found for the given date range.");
            }
        } else {
            writer.println("Invalid CheckStatement command format. Please use 'CheckStatement date_from date_to'.");
        }
    }


    private static String checkStatement(String dateFrom, String dateTo, String username) {
        String memberId = getUserIdByUsername(username);
        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD)) {
            String selectSql = "SELECT d.receiptNo, d.amount, d.status, d.updated_at FROM deposits d JOIN members m ON d.memberId = m.memberId WHERE m.username = ? AND d.updated_at BETWEEN ? AND ? GROUP BY d.receiptNo, d.amount, d.status, d.updated_at;";
            PreparedStatement selectStatement = connection.prepareStatement(selectSql);
            selectStatement.setString(1, username);
            selectStatement.setString(2, dateFrom);
            selectStatement.setString(3, dateTo);
            ResultSet resultSet = selectStatement.executeQuery();
    
            double loanper = getLoanProgress(memberId);
            double contributionper = getperf(memberId);
            double accountBalance = getBalance(memberId);
    
            StringBuilder statementData = new StringBuilder();
            boolean foundTransactions = false;
    
            while (resultSet.next()) {
                double amount = resultSet.getDouble("amount");
                String date = resultSet.getString("updated_at");
                String receiptNumber = resultSet.getString("receiptNo");
                String status = resultSet.getString("status");
    
                statementData.append(String.format("Transaction: %s\tAmount: %-10.2f\tDate Deposited: %-15s\tReceipt Number: %-20s\t",
                        status, amount, date, receiptNumber));
                foundTransactions = true;
            }
    
            resultSet.close();
            selectStatement.close();
    
            if (foundTransactions) {
                int lastIndex = statementData.length();
                statementData.insert(lastIndex, String.format("\tAccount Balance: %-10.2f", accountBalance));
                statementData.insert(lastIndex, String.format("\tContribution Status: %-10.2f%%", contributionper));
                statementData.insert(lastIndex, String.format("\tLoan Progress: %-10.2f%%\t", loanper));
            } else {
                statementData.append("No transactions found for the given date range.");
            }
    
            return statementData.toString();
        } catch (SQLException e) {
            e.printStackTrace();
            return "Error";
        }
    }
    


    public static double getLoanProgress(String memberId) throws SQLException {
        double per = 0.0;
        String query = "SELECT performance FROM loans WHERE memberId = ?";


        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(query)) {
            statement.setString(1, memberId);

            try (ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    per = resultSet.getDouble("performance");
                }
            }
        }

        return per;
    }

    public static double getperf(String memberId) throws SQLException {
        double per = 0.0;
        String query = "SELECT performance FROM members WHERE memberId = ?";


        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(query)) {
            statement.setString(1, memberId);

            try (ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    per = resultSet.getDouble("performance");
                }
            }
        }

        return per;
    }
 // Fetches the account balance from the database based on the receipt number
    public static double getBalance(String memberId) throws SQLException {
        double balance = 0.0;
        String query = "SELECT balance FROM members WHERE memberId = ?";
               

        try (Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement statement = connection.prepareStatement(query)) {
            statement.setString(1, memberId);

            try (ResultSet resultSet = statement.executeQuery()) {
                if (resultSet.next()) {
                    balance = resultSet.getDouble("balance");
                }
            }
        }

        return balance;
    }

    private static boolean validateMemberDetails(String memberNumber, String phoneNumber, PrintWriter outToClient) {
        try {
           

            Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
            Statement statement = connection.createStatement();
            String query = "SELECT * FROM members WHERE memberId = '" + memberNumber + "' AND phoneNumber = '" + phoneNumber + "'";
            ResultSet resultSet = statement.executeQuery(query);

            boolean detailsMatch = resultSet.next();

            if (detailsMatch) {
                // Member details match, retrieve the password
                String passwordRetrieved = resultSet.getString("password");
              System.out.println("Your password is: " + passwordRetrieved + " Enter login command:");
            } else {
                // No match found for member details
               // outToClient.println("Invalid member number or phone number.");
               // outToClient.println("Please contact the web system administrator for assistance.");
            }

            resultSet.close();
            statement.close();
            connection.close();

            return detailsMatch;
        } catch (SQLException e) {
            System.out.println("An SQLException occurred: " + e.getMessage());
            return false;
        }
    }
    private static String retrievePassword(String memberNumber, PrintWriter outToClient) {
        try {
          

            Connection connection = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
            Statement statement = connection.createStatement();
            String query = "SELECT password FROM members WHERE memberId = '" + memberNumber + "'";
            ResultSet resultSet = statement.executeQuery(query);

            if (resultSet.next()) {
                String passwordRetrieved = resultSet.getString("password");
               outToClient.println("Your password is: " + passwordRetrieved);
               return passwordRetrieved;
            } else {
                System.out.println("Invalid member number. Please try again.");
            }

            resultSet.close();
            statement.close();
            connection.close();
        } catch (SQLException e) {
            System.out.println("An SQLException occurred: " + e.getMessage());
        }
        return null;
   
    }
    private static  String generateReferenceNumber() {
        String referenceNumber = "";
    
        for (int i = 0; i < 6; i++) {
            int randomNumber = (int) (Math.random() * 10);
            referenceNumber += Integer.toString(randomNumber);
        }
    
        return referenceNumber;
    }




  


    public static void main(String[] args) {
        Socket socket = null;
        BufferedReader inFromClient = null;
        PrintWriter outToClient = null;

        try (ServerSocket serverSocket = new ServerSocket(8181)) {
            System.out.println("Server is running and waiting for a client...");

            while (true) {
                socket = serverSocket.accept();
                System.out.println("Client connected.");
                

                inFromClient = new BufferedReader(new InputStreamReader(socket.getInputStream()));
                outToClient = new PrintWriter(socket.getOutputStream(), true);
 
                                String welcomeMessage = "Welcome to Uprise-sacco Management System\t"
                                + "Please register or login to continue.";

                        // Send the combined message to the client
                        outToClient.println(welcomeMessage);

                        boolean isLoggedIn = false;
                        String username = null;

                        while (true) {
                            String command = inFromClient.readLine();
                            System.out.println("Client: " + command);

                            if (command == null) {
                                System.out.println("Client connection closed abruptly.");
                                return;
                            }
                            if (command.equals("logout")) {
                                outToClient.println("SessionTermination!");
                                break;
                            }
                            // if (isLoggedIn && !command.startsWith("register")) {
                            //     // Prompt user to register or login before performing any other command
                            //     writer.println("You need to register or login to access other commands.");
                            //     continue;
                            // }

                            if (command.startsWith("login")) {
                                String[] parts = command.split(" ");
                                if (parts.length == 3) {
                                    username = parts[1];
                                    String password = parts[2];
                                    // Perform authentication by checking against a MySQL database
                                    if (isValidCredentials(username, password)) {
                                        outToClient.println("Login successful!" + "You are logged in as " + username + ".");
                                        isLoggedIn = true;
                                        loggedInClients.put(username, true);
                                    } else {
                                        outToClient.println("Invalid username or password!. Enter MemberId ");
                                        String memberNumber = inFromClient.readLine();
                                        outToClient.println("Enter Phone number ");
                                        String phoneNumber = inFromClient.readLine();
                                        boolean detailsMatch = validateMemberDetails(memberNumber, phoneNumber, outToClient);
                                        if (detailsMatch) {
                                            // Proceed with password retrieval logic
                                            String passwordRetrieved = retrievePassword(memberNumber, outToClient);
                                            //outToClient.println(passwordRetrieved);
                                        } else {
                                          
                                            String referenceNumber = generateReferenceNumber();
    
                                             outToClient.println("Invalid phone number or member number. A reference number has been generated for you to use the next time you access the system. Your reference number is: " + referenceNumber);
     
                                        }
                                    }
                                } else {
                                    outToClient.println("Invalid login command!");
                                }

                            } else if (command.startsWith("register")) {
                               // handleRegisterCommand(command, outToClient, inFromClient);
                            } else if (!isLoggedIn) {
                                outToClient.println("You need to register or login to access other commands.");
                            } else {
                                String[] parts = command.split(" ");
                                String commandType = parts[0].toLowerCase(); // Convert to lowercase


                                switch (commandType) {
                                    case "deposit":
                                    handleDepositCommand(command, outToClient, username);
                                            break;
                                    case "payloan":
                                     handleLoanDepositCommand(command, outToClient,username); 
                                        break;
                                    case "checkstatement":
                                       handleCheckStatementCommand(command, outToClient, username);
                                        break;
                                    case "requestloan":
                                        handleRequestLoanCommand(command, outToClient, username);
                                        break;
                                    case "loanrequeststatus":
                                       handleLoanRequestStatusCommand(command, outToClient, username);
                                        break;
                                    case "accept":
                                       handleLoanAcceptCommand(command, outToClient, username);
                                        break;
                                    case "reject":
                                       handleLoanRejectCommand(command, outToClient, username);
                                        break;
                                    default:
                                        outToClient.println("Invalid command.");
                                }
                            }
                        }
                    }

                }catch (IOException e) {
                        e.printStackTrace();
                   } finally {
        try {
            if (inFromClient != null)
                inFromClient.close();
            if (outToClient != null)
                outToClient.close();
            if (socket != null)
                socket.close();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
}