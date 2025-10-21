import { useState, useEffect } from "react";
import { apiStore } from "../store";
import "../styles/AbsenceRequest.css"

export default function AbsenceRequest() {
    const [absenceType, setAbsenceType] = useState(null)
    const [dateDebut, setDateDebut] = useState('')
    const [timeUniqDay, setTimeUniqDay] = useState('fullday')
    const [dateFin, setDateFin] = useState(null)
    const [types, setTypes] = useState([]);
    const token = localStorage.getItem("authToken");
    const fetchTypeAbsence = async () => {
        const response = await fetch(apiStore.getState().getTypeAbsences(), {
            method: "GET",
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "application/json",
            },
        });
        if (response.ok) {
            const data = await response.json();
            setTypes(data.member || []);
        } else {
        
        }
    };

    useEffect(() => {
        fetchTypeAbsence();
    }, []);


    const absenceRequest = async (e) => {
        const requestData = {
            absenceType,
            dateDebut,
            dateFin: dateFin || null, 
            timeUniqDay: dateFin ? null : timeUniqDay, 
        };
        e.preventDefault()
        const response = await fetch(`${apiStore.getState().postAbsenceRequest()}`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestData)
        });
    }

    const handleChange = (e) => {
        const { name, checked } = e.target;
        if (checked) {
            setTimeUniqDay(name);  
        } else {
            setTimeUniqDay('fullday'); 
        }
    };

    const disableHalfDay = !!dateFin;
    return (
        <form onSubmit={absenceRequest}>
        <select
            name="absenceType"
            id="absenceType"
            onChange={(e) => setAbsenceType(e.target.value)}
        > 
            {types.map((type) => (
            <option key={type.id} value={type.id}>
                {type.label}
            </option>
            ))}
        </select>

        <label htmlFor="dateDebut">Sélectionnez une date de début</label>
        <input
            type="date"
            name="dateDebut"
            onChange={(e) => setDateDebut(e.target.value)}
        />

        {/* 🕐 Demi-journée : désactivée si dateFin renseignée */}
        <div style={{ opacity: disableHalfDay ? 0.5 : 1 }}>
            <label htmlFor="morning">Matinée</label>
            <input
            type="checkbox"
            id="morning"
            name="morning"
            onChange={handleChange}
            checked={timeUniqDay === "morning"}
            disabled={disableHalfDay}
            />

            <label htmlFor="afternoon">Après-midi</label>
            <input
            type="checkbox"
            id="afternoon"
            name="afternoon"
            onChange={handleChange}
            checked={timeUniqDay === "afternoon"}
            disabled={disableHalfDay}
            />
        </div>

        <label htmlFor="dateFin">Sélectionnez une date de fin (optionnelle)</label>
        <input
            type="date"
            name="dateFin"
            onChange={(e) => setDateFin(e.target.value)}
        />

        <button id="absenceRequestbutton" type="submit">
            Soumettre la demande
        </button>
        </form>
    );
}
