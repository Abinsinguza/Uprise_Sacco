package org.uprise.systems.views;

import java.util.Scanner;

public class LoginView {
    private Scanner scanner;

    public LoginView() {
        scanner = new Scanner(System.in);
    }

    public String getCommand() {
        System.out.print("Enter command (login username password): ");
        return scanner.nextLine();
    }

    public void displayMenu() {
        System.out.println("=== Sacco Performance Measurement and Monitoring ===");
        System.out.println("1. Deposit");
        System.out.println("2. Check Statement");
        System.out.println("3. Request Loan");
        System.out.println("4. Check Loan Request Status");
        System.out.println("5. Exit");
    }

    public int getChoice() {
        System.out.print("Enter your choice: ");
        return scanner.nextInt();
    }
}