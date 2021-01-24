import React from 'react';
import {connect} from 'react-redux';
import {components} from 'react-trello';
import {disableDependencyManager, enableDependencyManager} from '../../actions/programincrement/show';

const Card = ({enableDependencyManager, disableDependencyManager, workitem, store, ...rest}) => {
  function onMouseEnter() {
    enableDependencyManager(workitem);
  }

  return <div onMouseEnter={onMouseEnter} onMouseLeave={disableDependencyManager}>
    <components.Card {...rest}/>
  </div>;
}

const mapDispatchToProps = dispatch => ({
  enableDependencyManager: workitem => dispatch(enableDependencyManager(workitem)),
  disableDependencyManager: () => dispatch(disableDependencyManager()),
});

export default connect(null, mapDispatchToProps)(Card);
