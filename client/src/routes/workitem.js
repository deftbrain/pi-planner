import React from 'react';
import { Route } from 'react-router-dom';
import { List, Create, Update, Show } from '../components/workitem/';

export default [
  <Route path="/workitems/create" component={Create} exact key="create" />,
  <Route path="/workitems/edit/:id" component={Update} exact key="update" />,
  <Route path="/workitems/show/:id" component={Show} exact key="show" />,
  <Route path="/workitems/" component={List} exact strict key="list" />,
  <Route path="/workitems/:page" component={List} exact strict key="page" />
];
