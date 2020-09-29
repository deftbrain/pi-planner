import React from 'react';
import {Link} from 'react-router-dom';
import {getUserData} from './authProvider';
import {ADMIN_URL} from './config/app';

export default () => (
  <div>
    <h1>Hello, {getUserData().name}!</h1>
    <ul>
      <li><a href={ADMIN_URL} target="_blank">Admin Panel</a></li>
      <li><Link to="/program_increments/">Program Increments</Link></li>
    </ul>
  </div>
);
