import React, { useState } from "react";
import { apiStore, userStore } from "../store";
import '../styles/Tag.css';

export default function Tag() {
  const statut = userStore(state => state.statut);
  const setStatut = userStore(state => state.setStatut);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const handleClick = async () => {
    setLoading(true);
    setError(null);
    try {
      const token = localStorage.getItem("authToken");
      if (!token) throw new Error("No token found");

      const response = await fetch(apiStore.getState().getTag(), {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        }
      });

      if (!response.ok) throw new Error(`Error ${response.status}: ${response.statusText}`);

      const data = await response.json();
      setStatut(data.statut);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  // Classe dynamique selon le statut
  const statutClass =
    statut === 'present' ? 'statut-present' :
    statut === 'absent' ? 'statut-absent' :
    '';

  return (
    <div className="tag-container">
      <div className="statut-container">
        <p className={`statut-text ${statutClass}`}>
          Statut actuel : {statut}
        </p>
      </div>

      <button className="badger-button" onClick={handleClick} disabled={loading}>
        {loading ? "Loading..." : "Badger"}
      </button>

      {error && <p style={{ color: "red", marginTop: '12px' }}>Error: {error}</p>}
    </div>
  );
}
