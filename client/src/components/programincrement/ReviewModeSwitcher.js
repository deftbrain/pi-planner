import React from 'react';
import {connect} from 'react-redux';
import Switch from '@material-ui/core/Switch';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import {switchReviewMode} from '../../actions/programincrement/show';

const ReviewModeSwitcher = props => {
  const SAVED_STATE_KEY = 'reviewMode';

  const onChange = (value) => {
    localStorage.setItem(SAVED_STATE_KEY, value);
    props.switchReviewMode();
  }

  const savedState = localStorage.getItem(SAVED_STATE_KEY);
  if (savedState && savedState !== props.isReviewModeEnabled) {
    props.switchReviewMode();
  }

  return (
    <FormControlLabel label="Review mode" onClick={e => e.stopPropagation()} control={
      <Switch checked={props.isReviewModeEnabled} color="primary" onChange={event => onChange(event.target.value)}/>
    }/>
  );
}

const mapStateToProps = state => ({
  isReviewModeEnabled: state.programincrement.show.isReviewModeEnabled,
});

const mapDispatchToProps = dispatch => ({
  switchReviewMode: () => dispatch(switchReviewMode()),
});

export default connect(mapStateToProps, mapDispatchToProps)(ReviewModeSwitcher);
