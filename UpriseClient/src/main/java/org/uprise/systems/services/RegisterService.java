package org.uprise.systems.services;

import java.util.ArrayList;
import java.util.List;

import org.uprise.systems.models.User;

public class RegisterService {

    private List<User> userList;

    public RegisterService() {
        this.userList = new ArrayList<>();
    }

    public List<User> getUserList() {
        return userList;
    }

    public void setUserList(List<User> userList) {
        this.userList = userList;
    }

    public boolean registerUser(User newUser) {
        // Check if the username or email already exists in the list
        for (User user : userList) {
            if (user.getUsername().equals(newUser.getUsername()) || user.getEmail().equals(newUser.getEmail())) {
                return false; // Registration failed, username or email already exists
            }
        }

        userList.add(newUser);
        return true; // Registration successful
    }
}
