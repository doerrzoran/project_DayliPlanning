import React, { useEffect } from 'react';
import { apiStore } from '../store';

export default function TestApi() {
  const apiUrl = apiStore((state) => state.apiUrl);
  const isConnected = apiStore((state) => state.isConnected);
  const statusMessage = apiStore((state) => state.statusMessage);
  const setConnectionStatus = apiStore((state) => state.setConnectionStatus);

  useEffect(() => {
    fetch(apiUrl)  // endpoint d’healthcheck exemple
      .then(res => {
        if (res.ok) {
          setConnectionStatus(true, 'Connexion établie');
        } else {
          setConnectionStatus(false, 'Connexion échouée');
        }
      })
      .catch(() => {
        setConnectionStatus(false, 'Connexion échouée');
      });
  }, [apiUrl, setConnectionStatus]);

  return (
    <div>
      <p>URL API : {apiUrl}</p>
      <p>Status : {isConnected ? 'Connecté' : 'Non connecté'}</p>
      {statusMessage && <small>{statusMessage}</small>}
    </div>
  );
}
