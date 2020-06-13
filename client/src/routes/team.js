import React from 'react';
import { Route } from 'react-router-dom';
import { List, Create, Update, Show } from '../components/team/';

export default [
  <Route path="/teams/create" component={Create} exact key="create" />,
  <Route path="/teams/edit/:id" component={Update} exact key="update" />,
  <Route path="/teams/show/:id" component={Show} exact key="show" />,
  <Route path="/teams/" component={List} exact strict key="list" />,
  <Route path="/teams/:page" component={List} exact strict key="page" />
];
