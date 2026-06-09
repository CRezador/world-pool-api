import axios from "axios";
import api from "./api";
import type { User } from "../types/index";
import type { AxiosResponse } from "axios";

async function fetchCsrfCookie() {
    await axios.get("/sanctum/csrf-cookie", { withCredentials: true });
}

export const login = async (email: string, password: string, remember = false): Promise<void> => {
    await fetchCsrfCookie();
    await api.post("/login", { email, password, remember });
};

export const logout = async (): Promise<void> => {
    await api.delete("/logout");
};

export const register = async (name: string, email: string, password: string): Promise<User> => {
    await fetchCsrfCookie();
    const response: AxiosResponse<{ data: User }> = await api.post("/register", {
        name,
        email,
        password,
    });
    return response.data.data;
};