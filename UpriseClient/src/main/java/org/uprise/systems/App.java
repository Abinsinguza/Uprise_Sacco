package org.uprise.systems;

import org.uprise.systems.models.User;
import org.uprise.systems.services.LoginService;
import org.uprise.systems.services.RegisterService;
import org.uprise.systems.views.LoginView;
import org.uprise.systems.views.RegisterView;

import java.util.Scanner;

public class App {

    public static void main(String[] args) {
        Scanner scanner = new Scanner(System.in);
        RegisterService registerService = new RegisterService();
        LoginService loginService = new LoginService(registerService);
        LoginView loginView = new LoginView();
        RegisterView registerView = new RegisterView(registerService);

        boolean loggedIn = false;
        while (!loggedIn) {
            System.out.println("=== Sacco Performance Measurement and Monitoring ===");
            System.out.println("Enter command: 'login username password'");
            System.out.println("Enter command: 'register' to register a new user");
            System.out.println("Enter command: 'exit' to exit the Sacco system");

            System.out.print("Enter your command: ");
            String command = scanner.nextLine();

            String[] commandParts = command.split(" ");
            String action = commandParts[0].toLowerCase();

            switch (action) {
                case "login":
                    if (commandParts.length != 3) {
                        System.out.println("Invalid login command format. Please use 'login username password'.");
                        break;
                    }

                    String username = commandParts[1];
                    String password = commandParts[2];

                    User authenticatedUser = loginService.authenticate(username, password);
                    if (authenticatedUser != null) {
                        System.out.println("Login successful!");
                        // TODO: Implement the menu for authenticated users
                        // You can add additional methods in the User class to perform actions after login
                    } else {
                        System.out.println("Login failed. Incorrect username or password.");
                    }
                    break;
                case "register":
                    registerView.registerUser();
                    break;
                case "exit":
                    System.out.println("Exiting the Sacco system. Goodbye!");
                    System.exit(0);
                default:
                    System.out.println("Invalid command. Please try again.");
            }
        }
    }
}
