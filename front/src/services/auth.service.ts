import axios from "axios";
import api from "./api";
import type { User } from "../types/User";
import type { AxiosResponse } from "axios";

const csrfApi = axios.create({
    baseURL: "http://localhost:8080",
    withCredentials: true,
});

export const login = async (email: string, password: string): Promise<void> => {
    await csrfApi.get("/sanctum/csrf-cookie");
    await api.post("/login", { email, password });
};

export const logout = async (): Promise<void> => {
    await api.delete("/logout");
};

export const register = async (name: string, email: string, password: string): Promise<User> => {
    try {
        await csrfApi.get("/sanctum/csrf-cookie");
        const response: AxiosResponse<{ data: User }> = await api.post("/register", {
            name,
            email,
            password,
        });
        return response.data.data;
    } catch (error: unknown) {
        throw new Error((error as Error).message || "Registration failed");
    }
};
