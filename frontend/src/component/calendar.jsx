import React, { useEffect, useState } from 'react';
import { userStore } from "../store";
import '../styles/calendar-button.css'

import 'bootstrap-icons/font/bootstrap-icons.css';

import WeeklyPlanning from './WeeklyPlanning';
import MonthlyPlanning from './MonthlyPlanning';
import YearlyPlanning from './YearlyPlanning';
import { getMondayFromDate } from '../Utils/dateUtils';
import { useStore } from 'zustand';
import { useFormattedPeriods } from '../Utils/UseFormatedPeriods';

export default function Calendar() {
  const user = useStore(userStore, (state) => state.user);
   const exempleData = useFormattedPeriods(user);

  useEffect(() => {
    console.log(exempleData)
  }, [exempleData])


  const now = new Date();

  // 'week' | 'month' | 'year'
  const [view, setView] = useState('week');

  const toggleTo = (target) => setView(target);

  return (
    <div id='calendar' style={{ padding: 12 }}>
      

      {/* Toggle view */}
      <div style={{ marginBottom: 20, display: 'flex', gap: 8 }}>
        <button onClick={() => toggleTo('week')} className={view === 'week' ? 'active' : ''}>
          <i className="bi bi-calendar-week" /> Hebdo
        </button>
        <button onClick={() => toggleTo('month')} className={view === 'month' ? 'active' : ''}>
          <i className="bi bi-calendar3" /> Mensuel
        </button>
        <button onClick={() => toggleTo('year')} className={view === 'year' ? 'active' : ''}>
          <i className="bi bi-calendar4-week" /> Annuel
        </button>
      </div>

      <div>
        {view === 'week' && (
          <>
            <h2>Vue hebdomadaire</h2>
            <WeeklyPlanning
              exempleData={exempleData}
              now={now}
              getMondayFromDate={getMondayFromDate}
            />
          </>
        )}

        {view === 'month' && (
          <>
            <h2>Vue mensuelle</h2>
            <MonthlyPlanning
              exempleData={exempleData}
              now={now}
              getMondayFromDate={getMondayFromDate}
            />
          </>
        )}

        {view === 'year' && (
          <>
            <h2>Vue annuelle</h2>
            <YearlyPlanning
              exempleData={exempleData}
              now={now}
            />
          </>
        )}
      </div>
    </div>
  );
}
