import React from 'react';
import ReactDOM from 'react-dom';
import {Provider} from 'react-redux';
import {Route, Switch} from 'react-router-dom';
import {ConnectedRouter} from 'connected-react-router';

import * as serviceWorker from './serviceWorker';
import Welcome from './Welcome';
import programincrementRoutes from './routes/programincrement';
import {history, store} from './store';
import PrivateRoute from './PrivateRoute';
import Login from './Login';

ReactDOM.render(
  <Provider store={store}>
    <ConnectedRouter history={history}>
      <Switch>
        <Route path="/login" component={Login}/>
        <PrivateRoute path="/" component={Welcome} strict={true} exact={true}/>
        {programincrementRoutes}
        <Route render={() => <h1>Not Found</h1>}/>
      </Switch>
    </ConnectedRouter>
  </Provider>,
  document.getElementById('root')
);

// If you want your app to work offline and load faster, you can change
// unregister() to register() below. Note this comes with some pitfalls.
// Learn more about service workers: https://bit.ly/CRA-PWA
serviceWorker.unregister();
