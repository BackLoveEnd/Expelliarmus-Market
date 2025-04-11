import api from "@/utils/api.js";
import managerApi from "@/utils/managerApi.js";

export const login = async (data, manager) => {
    if (manager) {
        return api().post("/management/managers/auth/login", {
            email: data.email.toString(),
            password: data.password.toString(),
        });
    }

    return api().post("/login", {
        email: data.email.toString(),
        password: data.password.toString(),
    });
};

export const logout = async (manager) => {
    if (manager) {
        return managerApi().post("/managers/auth/logout");
    }

    return api().post("/logout");
};

export const register = async (user) => {
    return api().post("/register", {
        first_name: user.first_name,
        email: user.email,
        password: user.password,
        password_confirmation: user.password_confirmation,
    });
};

export const managerRegister = async (manager) => {
    return managerApi().post("/managers/auth/register", {
        first_name: manager.first_name,
        last_name: manager.last_name,
        email: manager.email,
    });
};

export const forgotPassword = async (email) => {
    return api().post("/forgot-password", {
        email: email,
    });
};

export const resetPassword = async (reset, token) => {
    return api().post("/reset-password", {
        email: reset.email,
        password: reset.password,
        password_confirmation: reset.password_confirmation,
        token: token,
    });
};
