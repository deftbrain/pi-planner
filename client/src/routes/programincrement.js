import React from 'react';
import {List, Show} from '../components/programincrement/';
import PrivateRoute from '../PrivateRoute';

export default [
  <PrivateRoute path="/program_increments/show/:id" component={Show} exact key="show"/>,
  <PrivateRoute path="/program_increments/" component={List} exact strict key="list"/>,
  <PrivateRoute path="/program_increments/:page" component={List} exact strict key="page"/>
];
