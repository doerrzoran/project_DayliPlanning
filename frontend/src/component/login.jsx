import { useState } from "react";
import { apiStore } from "../store";
import GetUser from "./GetUser";
import { useNavigate } from "react-router";

export default function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const setConnectionStatus = apiStore((state) => state.setConnectionStatus);
  const getTokenUrl = apiStore((state) => state.getTokenUrl);
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
  e.preventDefault();

  try {
    const response = await fetch(getTokenUrl(), {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ email, password }),
    });

    if (!response.ok) {
      throw new Error("Erreur de connexion");
    }

    const data = await response.json();
    console.log("Token reçu :", data.token);

    localStorage.setItem("authToken", data.token);

    setConnectionStatus(true, "Connexion réussie !");

    navigate("/calendar"); 
      } catch (error) {
        console.error(error);
        setConnectionStatus(false, "Identifiants incorrects");
      }
    };


  return (
    <div style={{ maxWidth: "400px", margin: "2rem auto" }}>
      <h2 id="connexion">Connexion</h2>
      <GetUser/>
      <form onSubmit={handleSubmit}>
        <div style={{ marginBottom: "1rem" }}>
          <label>Email :</label>
          <input
            type="email"
            required
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            style={{ width: "100%" }}
          />
        </div>

        <div style={{ marginBottom: "1rem" }}>
          <label>Mot de passe :</label>
          <input
            type="password"
            required
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            style={{ width: "100%" }}
          />
        </div>

        <button type="submit">Se connecter</button>
      </form>
    </div>
  );
}
