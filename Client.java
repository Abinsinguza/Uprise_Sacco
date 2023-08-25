import java.io.*;
import java.net.*;


public class Client {
    private static BufferedReader reader;
    private static PrintWriter writer;
   

    public static void main(String[] args) {
        try (Socket socket = new Socket("localhost", 8181)) {
            // Initialize reader and writer
            reader = new BufferedReader(new InputStreamReader(socket.getInputStream()));
            writer = new PrintWriter(socket.getOutputStream(), true);

            boolean isLoggedIn = false;

            while (true) {
                String commandPrompt = readServerResponse();
               System.out.println(commandPrompt);

                if (commandPrompt.startsWith("Welcome to Uprise-sacco Management System")) {
                    if (!isLoggedIn) {
                        menu();
                       // printCommandSyntax();
                        isLoggedIn = processLogin();
                    } else if (commandPrompt.startsWith("StatementData:")) {
                        isLoggedIn = true;
                        // Process and print statement table
                        String statementData = commandPrompt.substring("StatementData: ".length());
                        printStatementTable(statementData);
                     } else if (commandPrompt.equals("Congratulations! Your loan request has been approved.")) {
                        menu(); // Display the menu when loan is approved
                        String menuChoice = readUserInput();
                        sendCommandToServer(menuChoice);
                    }
                        else if (commandPrompt.startsWith("Registration successful!") & isLoggedIn){
                            String command = readUserInput();
                        sendCommandToServer(command);
                   
                        }else {
                        String command = readUserInput();
                        sendCommandToServer(command);
                    }
                }
                else if (commandPrompt.startsWith("Registration successful!") & isLoggedIn){

                     String command = readUserInput();
                        sendCommandToServer(command);
                }
               
                 else if (commandPrompt.equals("SessionTermination!")) {
                    isLoggedIn=true;
                        System.out.println("Logging out. Exiting...");
                        break;
                    }                
                else {
                    isLoggedIn = true;
                    String command = readUserInput();
                    sendCommandToServer(command); 
                                  
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    private static boolean processLogin() throws IOException {
        String command = readUserInput();
        sendCommandToServer(command);
        String serverResponse = readServerResponse();
         System.out.println( serverResponse);

        if (serverResponse.startsWith("Login successful!") || serverResponse.startsWith("Deposit successful!")
                || serverResponse.startsWith("Loan application submitted.")) {
                    printCommandSyntax();
            String commandAfterLogin = readUserInput();
            sendCommandToServer(commandAfterLogin);
            
            return true;
        } else if (serverResponse.startsWith("StatementData: ")) {
            String statementData = serverResponse.substring("StatementData: ".length());
            printStatementTable(statementData);
            return true;
        }
        
        else if (serverResponse.startsWith("Registration successful!")){
            String commandAfterInvalidLogin = readUserInput();
            sendCommandToServer(commandAfterInvalidLogin);
            return true;       
        }
         else {
            String commandAfterInvalidLogin = readUserInput();
            sendCommandToServer(commandAfterInvalidLogin);
            return false;
        }
    }

    private static void printStatementTable(String statementData) {
        
    }
    

    private static String readServerResponse() throws IOException {
        return reader.readLine();
    }

    private static String readUserInput() {
        BufferedReader bufferedReader = new BufferedReader(new InputStreamReader(System.in));
        try {
            System.out.print("Enter Command:");
            return bufferedReader.readLine();
        } catch (IOException e) {
            e.printStackTrace();
            return "Error occured";
        }
    }

    private static void sendCommandToServer(String command) {
        writer.println(command);
    }

    private static void printCommandSyntax() {
        System.out.println("Menu items and Command Syntax \n" +
        "1. login username password  \n" +
        "2. deposit amount date receiptNo \n" +
        "3. CheckStatement datefrom dateTo \n" +
        "4. LoanRequestStatus applicatioNumber\n" +
        "5. register  *******************   \n"+
        "6. LoanAccept  \n"+
        "7. loanReject \n"+
        "8. LoanDeposit ************************ \n");
    }
    private static void menu() {
        System.out.println("WELCOME TO UPRISE SACCO BANKING SYSTEM.\n"
        + "Please login to view the menu:\n"
        + "LOGIN <USERNAME> <PASSWORD> ");
        System.out.print("Enter login command: ");
    }

    private static void menu2() {
        System.out.println("Congratulations! Your loan request has been approved.\n"
                + "Please choose an option:\n"
                + "1. Accept\n"
                + "2. Reject");

        System.out.print("Enter your choice: ");
    }
}

