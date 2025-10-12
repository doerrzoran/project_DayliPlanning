import { useState } from "react";
import { apiStore } from "../store";

export default function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const setConnectionStatus = apiStore((state) => state.setConnectionStatus);
  const getTokenUrl = apiStore((state) => state.getTokenUrl);

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

      // 🔐 Enregistrer le token dans localStorage
      localStorage.setItem("authToken", data.token);

      // 🟢 Mettre à jour Zustand
      setConnectionStatus(true, "Connexion réussie !");
    } catch (error) {
      console.error(error);
      setConnectionStatus(false, "Identifiants incorrects");
    }
  };

  return (
    <div style={{ maxWidth: "400px", margin: "2rem auto" }}>
      <h2>Connexion</h2>
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
