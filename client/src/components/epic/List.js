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
import Teams from './Teams';

const styles = theme => ({
  root: {
    width: '100%',
  },
  heading: {
    fontSize: theme.typography.pxToRem(15),
    fontWeight: theme.typography.fontWeightRegular,
  },
  projectName: {
    fontSize: '1.5rem',
    padding: `${theme.spacing(3)}px ${theme.spacing(1)}px ${theme.spacing(1)}px`,
  },
  teams: {
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
    reset: PropTypes.func.isRequired,
    teams: PropTypes.object,
  };

  componentDidMount() {
    const projectIds = this.props.programIncrement.projectsSettings.map(ps => ps['project']);
    const projectSettingsIds = this.props.programIncrement.projectsSettings.map(ps => ps['@id']);
    this.props.list(projectIds, projectSettingsIds);
  }

  componentWillUnmount() {
    this.props.reset(this.props.eventSource);
  }

  groupEpicsByProject(epics) {
    const groupedEpics = {};
    for (let epic of epics) {
      if (!groupedEpics[epic.project]) {
        groupedEpics[epic.project] = [epic];
      } else {
        groupedEpics[epic.project].push(epic);
      }
    }
    return groupedEpics;
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
          {this.props.retrieved && Object.entries(this.groupEpicsByProject(this.props.retrieved['hydra:member']
            .filter(e => !this.props.selectedTeam || e.teams.includes(this.props.selectedTeam)))).map(([project, epics]) =>
            <div key={project}>
              {this.props.projects &&
              <Typography variant="h2" className={this.props.classes.projectName}>
                {(this.props.projects['hydra:member'] || []).find(p => p['@id'] === project).name}
              </Typography>
              }
              {epics.map(item =>
                <ExpansionPanel key={item['@id']} TransitionProps={{unmountOnExit: true}}>
                  <ExpansionPanelSummary expandIcon={<ExpandMoreIcon/>}>
                    <Typography className={this.props.classes.heading}>
                      <ExternalLink entity={item}/>
                      {this.props.teams && (
                        <Teams
                          teams={(this.props.teams['hydra:member'] || []).filter(t => item.teams.includes(t['@id']))}
                          className={this.props.classes.teams}/>
                      )}
                    </Typography>
                  </ExpansionPanelSummary>
                  <ExpansionPanelDetails>
                    <WorkitemList epic={item} programIncrement={this.props.programIncrement}/>
                  </ExpansionPanelDetails>
                </ExpansionPanel>
              )}
            </div>
          )}
        </div>
      </div>
    );
  }
}

const mapStateToProps = state => {
  return {
    retrieved: state.epic.list.retrieved,
    loading: state.epic.list.loading,
    error: state.epic.list.error,
    eventSource: state.epic.list.eventSource,
    deletedItem: state.epic.list.deletedItem,
    projects: state.project.list.retrieved,
    teams: state.team.list.retrieved,
    selectedTeam: state.programincrement.show.teamFilter
  };
};

const mapDispatchToProps = dispatch => ({
  list: (projectIds, projectSettingsIds) => dispatch(list(projectIds, projectSettingsIds)),
  reset: eventSource => dispatch(reset(eventSource))
});

export default connect(mapStateToProps, mapDispatchToProps)(withStyles(styles)(List));
