import React from 'react';
import { useEffect } from 'react';

export default function Calendar(){
    const now = new Date();
        this.state = {
            year: props.year ?? now.getFullYear(),
            month: props.month ?? now.getMonth(),
            view: props.view ?? 'month',
            weekStartsOn: typeof props.weekStartsOn === 'number' ? props.weekStartsOn : 1,
            dates: [],
            events: props.events ?? [],
            eventsByDay: {}
        };

        useEffect(() => {
            console.log(now)
        })
}

