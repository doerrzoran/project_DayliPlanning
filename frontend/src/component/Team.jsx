import React, { useEffect } from 'react';
import { userStore, teamStore } from '../store';
import { apiStore } from '../store';

export default function Team() {
  const user = userStore((state) => state.user);
  const role = user?.role || null;
  const team = teamStore((state) => state.team);
  const setTeam = teamStore((state) => state.setTeam);

  useEffect(() => {
    async function fetchTeam() {
      if (role === 'ROLE_CADRE') {
        const response = await fetch(apiStore.getState().getTeam(), {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('authToken')}`,
          },
        });
        if (response.ok) {
          const data = await response.json();
          const sortedTeam = data.team.sort((a, b) => a.name.localeCompare(b.name));
          setTeam(sortedTeam);
        }
      } else {
        setTeam([]);
      }
    }
    fetchTeam();
  }, [role, setTeam]);

  if (!role) return <p>Chargement utilisateur...</p>;
  if (role !== 'ROLE_CADRE') return <p>Accès réservé aux cadres.</p>;

  const membre = team.find((m) => m.name === 'Durand' && m.firstname === 'Paul');

  function regrouperPeriodesAbsences(absences) {
  if (!absences || absences.length === 0) return [];

  const sorted = [...absences].sort((a, b) => new Date(a.date) - new Date(b.date));
  const result = [];
  let currentPeriode = {...sorted[0], dateFin: sorted[0].date};

  for (let i = 1; i < sorted.length; i++) {
    const absence = sorted[i];
    const prevDate = new Date(currentPeriode.dateFin);
    const currDate = new Date(absence.date);

    const diffDays = (currDate - prevDate) / (1000 * 60 * 60 * 24);

    if (
      diffDays === 1 &&
      absence.absenceType.id === currentPeriode.absenceType.id &&
      absence.halfDay.label === currentPeriode.halfDay.label
    ) {
      currentPeriode.dateFin = absence.date;
    } else {
      result.push(currentPeriode);
      currentPeriode = {...absence, dateFin: absence.date};
    }
  }
  result.push(currentPeriode);

  return result;
}

const absencesAcceptees = membre.absences
  .filter(a => a.isAccepted)
  .sort((a, b) => new Date(a.date) - new Date(b.date));
const periodesAcceptees = regrouperPeriodesAbsences(absencesAcceptees);

const absencesEnAttente = membre.absences
  .filter(a => !a.isAccepted)
  .sort((a, b) => new Date(a.date) - new Date(b.date));
const periodesEnAttente = regrouperPeriodesAbsences(absencesEnAttente);

async function handleValidateAbsence(ids) {
  const token = localStorage.getItem('authToken');
  const response = await fetch(apiStore.getState().postValidateAbsence(), {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ ids }), 
  });

  if (response.ok) {
    alert('Absences validées avec succès');
    window.location.reload();

  } else {
    alert('Erreur lors de la validation.');
  }
}

  return (
    <div>
      <h2>Détails de Paul Durand</h2>
      {!membre ? (
        <p>Les données de Paul Durand ne sont pas disponibles.</p>
      ) : (
        <div>
          <h3>{membre.firstname} {membre.name}</h3>
          <p>Email : {membre.email}</p>

          <h4>Absences acceptées</h4>
{periodesAcceptees.length > 0 ? (
  <ul>
    {periodesAcceptees.map((p, idx) => (
      <li key={idx}>
        Du {p.date} au {p.dateFin} – {p.absenceType.label} 
      </li>
    ))}
  </ul>
) : (
  <p>Aucune absence acceptée.</p>
)}

<h4>Demande d'absences</h4>
{periodesEnAttente.length > 0 ? (
  <ul>
    {periodesEnAttente.map((p, idx) => {
      // Récupère tous les IDs des absences de la période p entre date et dateFin
      const idsPeriode = membre.absences
        .filter(a => a.date >= p.date && a.date <= p.dateFin && !a.isAccepted)
        .map(a => a.id);

      return (
        <li key={idx}>
          Du {p.date} au {p.dateFin} – {p.absenceType.label}
          <button 
            style={{marginLeft: '1rem'}} 
            onClick={() => handleValidateAbsence(idsPeriode)}
          >
            Valider cette absence
          </button>
        </li>
      );
    })}
  </ul>
) : (
  <p>Aucune absence en attente.</p>
)}

        </div>
      )}
    </div>
  );
}
