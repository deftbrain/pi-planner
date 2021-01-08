import React from 'react';
import {Link} from 'react-router-dom';
import {getUserData} from './authProvider';
import './app.css';

export default () => (
  <div>
    <h1>Hello, {getUserData().name}!</h1>
    <ul>
      <li><Link to="/program_increments/">Program Increments</Link></li>
    </ul>
  </div>
);
