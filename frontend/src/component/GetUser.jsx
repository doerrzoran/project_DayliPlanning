import React, { useEffect, useState } from "react";
import { apiStore, userStore } from "../store";


export default function GetUser() {
  const [user, setUser] = useState(null);
  const [error, setError] = useState(null);
  const setUserStore = userStore((state) => state.setUser);
  const email = userStore((state) => state.user?.email);
  
  useEffect(() => {
    const fetchUser = async () => {
      try {
        const token = localStorage.getItem("authToken");
        if (!token) {
          setError("No token found in localStorage");
          return;
        }
        const response = await fetch(apiStore.getState().getMe(), {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json"
          }
        });

        if (!response.ok) {
          throw new Error(`Error ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        setUser(data);

        // Stocker dans Zustand userStore
        setUserStore(data);

        apiStore.getState().setConnectionStatus(true, "Connected to API");
      } catch (err) {
        setError(err.message);
        apiStore.getState().setConnectionStatus(false, err.message);
      }
    };

    fetchUser();
  }, [setUserStore]);


  if (error) return <div style={{ color: "red" }}>Error: {error}</div>;
  if (!user) return <div>Loading...</div>;

  return (
    <div>     
      <p>Email: {email}</p>
    </div>
  );
}
