import React from 'react';
import {connect} from 'react-redux';
import Switch from '@material-ui/core/Switch';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import {switchReviewMode} from '../../actions/programincrement/show';

const ReviewModeSwitcher = props => (
  <FormControlLabel label="Review mode" onClick={e => e.stopPropagation()} control={
    <Switch checked={props.isReviewModeEnabled} color="primary" onChange={props.switchReviewMode}/>
  }/>
);

const mapStateToProps = state => ({
  isReviewModeEnabled: state.programincrement.show.isReviewModeEnabled,
});

const mapDispatchToProps = dispatch => ({
  switchReviewMode: () => dispatch(switchReviewMode()),
});

export default connect(mapStateToProps, mapDispatchToProps)(ReviewModeSwitcher);
