import React from 'react';
import { Route } from 'react-router-dom';
import { List, Create, Update, Show } from '../components/epic/';

export default [
  <Route path="/epics/create" component={Create} exact key="create" />,
  <Route path="/epics/edit/:id" component={Update} exact key="update" />,
  <Route path="/epics/show/:id" component={Show} exact key="show" />,
  <Route path="/epics/" component={List} exact strict key="list" />,
  <Route path="/epics/:page" component={List} exact strict key="page" />
];
