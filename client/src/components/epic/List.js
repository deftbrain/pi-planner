import React, {Component} from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import {list, reset} from '../../actions/epic/list';
import WorkitemList from '../workitem/List';
import ExternalLink from '../entity/ExternalLink';
import {withStyles} from '@material-ui/core/styles';
import ExpansionPanel from '@material-ui/core/ExpansionPanel';
import ExpansionPanelSummary from '@material-ui/core/ExpansionPanelSummary';
import ExpansionPanelDetails from '@material-ui/core/ExpansionPanelDetails';
import Typography from '@material-ui/core/Typography';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore'
import WSJF from './WSJF';

const styles = theme => ({
  root: {
    width: '100%',
  },
  heading: {
    fontSize: theme.typography.pxToRem(15),
    fontWeight: theme.typography.fontWeightRegular,
  },
  wsjf: {
    fontSize: theme.typography.pxToRem(12),
    marginLeft: '10px',
  },
});

class List extends Component {
  static propTypes = {
    classes: PropTypes.object.isRequired,
    programIncrement: PropTypes.object.isRequired,
    retrieved: PropTypes.object,
    loading: PropTypes.bool.isRequired,
    error: PropTypes.string,
    eventSource: PropTypes.instanceOf(EventSource),
    deletedItem: PropTypes.object,
    list: PropTypes.func.isRequired,
    reset: PropTypes.func.isRequired
  };

  componentDidMount() {
    const projects = [this.props.programIncrement.project];
    this.props.list(projects);
  }

  componentWillUnmount() {
    this.props.reset(this.props.eventSource);
  }

  render() {
    return (
      <div>
        {this.props.loading && (
          <div className="alert alert-info">Loading...</div>
        )}
        {this.props.deletedItem && (
          <div className="alert alert-success">
            {this.props.deletedItem['@id']} deleted.
          </div>
        )}
        {this.props.error && (
          <div className="alert alert-danger">{this.props.error}</div>
        )}

        <div className={this.props.classes.root}>
          {this.props.retrieved && this.props.retrieved['hydra:member'].map(item => (
            <ExpansionPanel key={item['@id']} TransitionProps={{unmountOnExit: true}}>
              <ExpansionPanelSummary expandIcon={<ExpandMoreIcon/>}>
                <Typography className={this.props.classes.heading}>
                  <ExternalLink entity={item}/>
                  <WSJF value={item.wsjf} className={this.props.classes.wsjf}/>
                </Typography>
              </ExpansionPanelSummary>
              <ExpansionPanelDetails>
                  <WorkitemList epic={item['@id']} programIncrement={this.props.programIncrement}/>
              </ExpansionPanelDetails>
            </ExpansionPanel>
          ))}
        </div>
      </div>
    );
  }
}

const mapStateToProps = state => {
  const {
    retrieved,
    loading,
    error,
    eventSource,
    deletedItem
  } = state.epic.list;
  return { retrieved, loading, error, eventSource, deletedItem };
};

const mapDispatchToProps = dispatch => ({
  list: projects => dispatch(list(projects)),
  reset: eventSource => dispatch(reset(eventSource))
});

export default connect(mapStateToProps, mapDispatchToProps)(withStyles(styles)(List));
