import React from 'react';
import {Route} from 'react-router-dom';
import {List, Show} from '../components/programincrement/';

export default [
  <Route path="/program_increments/show/:id" component={Show} exact key="show" />,
  <Route path="/program_increments/" component={List} exact strict key="list" />,
  <Route path="/program_increments/:page" component={List} exact strict key="page" />
];
