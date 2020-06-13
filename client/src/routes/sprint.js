import React from 'react';
import { Route } from 'react-router-dom';
import { List, Create, Update, Show } from '../components/sprint/';

export default [
  <Route path="/sprints/create" component={Create} exact key="create" />,
  <Route path="/sprints/edit/:id" component={Update} exact key="update" />,
  <Route path="/sprints/show/:id" component={Show} exact key="show" />,
  <Route path="/sprints/" component={List} exact strict key="list" />,
  <Route path="/sprints/:page" component={List} exact strict key="page" />
];
