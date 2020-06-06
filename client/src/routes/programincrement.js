import React from 'react';
import { Route } from 'react-router-dom';
import { List, Create, Update, Show } from '../components/programincrement/';

export default [
  <Route path="/program_increments/create" component={Create} exact key="create" />,
  <Route path="/program_increments/edit/:id" component={Update} exact key="update" />,
  <Route path="/program_increments/show/:id" component={Show} exact key="show" />,
  <Route path="/program_increments/" component={List} exact strict key="list" />,
  <Route path="/program_increments/:page" component={List} exact strict key="page" />
];
