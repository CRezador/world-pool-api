import api from "./api";
import type { User } from "../types/User";
import type { AxiosResponse } from "axios";

export const register = async (name: string, email: string, password: string): Promise<User> => {
    try {
        const response: AxiosResponse<User> = await api.post("/register", {
            name,
            email,
            password,
        });
        return response.data;
    } catch (error) {
        throw new Error("Register failed");
    }
};

export const login = async (username: string, password: string): Promise<User> => {
    try {
        const response: AxiosResponse<User> = await api.post("/login", {
            email: username,
            password: password,
        });
        return response.data;
    } catch (error) {
        throw new Error("Login failed");
    }
};