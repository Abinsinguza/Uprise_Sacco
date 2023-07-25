package org.uprise.systems.services;

import org.uprise.systems.models.User;

import java.util.List;

public class LoginService {
    private List<User> users;

    public LoginService(RegisterService registerService) {
        this.users = registerService.getUserList();
    }

    public User authenticate(String username, String password) {
        for (User user : users) {
            if (user.getUsername().equals(username) && user.getPassword().equals(password)) {
                return user; // Return the authenticated user
            }
        }

        return null; // Return null if authentication fails
    }
}
