import {applyMiddleware, combineReducers, createStore} from 'redux';
import thunk from 'redux-thunk';
import {reducer as form} from 'redux-form';
import {createBrowserHistory} from 'history';
import {connectRouter, routerMiddleware} from 'connected-react-router';
import programincrement from './reducers/programincrement/';
import estimate from './reducers/programincrement/estimate/';
import project from './reducers/project/';
import team from './reducers/team/';
import sprint from './reducers/sprint/';
import epic from './reducers/epic/';
import workitem from './reducers/workitem/';
import backlogGroup from './reducers/backloggroup';

const history = createBrowserHistory();
const store = createStore(
  combineReducers({
    router: connectRouter(history),
    form,
    programincrement,
    estimate,
    project,
    team,
    sprint,
    epic,
    workitem,
    backlogGroup,
  }),
  applyMiddleware(routerMiddleware(history), thunk)
);

export {history, store};
