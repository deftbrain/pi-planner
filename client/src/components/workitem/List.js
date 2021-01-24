import React, {Component} from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import sortBy from 'lodash/sortBy';
import {list, reset} from '../../actions/workitem/list';
import Board from 'react-trello';
import ExternalLink from '../entity/ExternalLink';
import './List.css'
import {update} from '../../actions/workitem/update';
import Estimate from './Estimate';
import NewWorkitemForm from './Create';
import upperFirst from 'lodash/upperFirst';
import {withStyles} from '@material-ui/styles';
import Card from './Card';
import {store} from '../../store';

const styles = ({
  root: {
    width: '100%',
  },
  card: {
    minWidth: 'inherit !important',
    maxWidth: 'inherit !important',
    '& > header': {
      marginBottom: 0,
      borderBottom: 0,
      paddingTop: 3,
    },
    '& > div': {
      textAlign: 'right',
      border: 0,
    },
    '& > header span:first-child': {
      width: '100%',
      '& a': {
        fontWeight: 'normal',
      }
    },
    '& > header span:nth-child(2)': {
      width: '100%',
      position: 'absolute',
      top: 0,
      right: '-10px',
      // opacity: 0.5,
    },
    '&$dependency, &$dependant, &$dependantDependency': {
      '& > header span:first-child a': {
        color: 'white',
      }
    }
  },
  dependency: {
    backgroundColor: '#c51162 !important',
  },
  dependant: {
    backgroundColor: '#f57c00 !important',
  },
  dependantDependency: {
    backgroundColor: 'red !important',
  },
});

class List extends Component {
  static propTypes = {
    epic: PropTypes.object.isRequired,
    projects: PropTypes.array.isRequired,
    teams: PropTypes.object,
    sprints: PropTypes.object,
    programIncrement: PropTypes.object.isRequired,
    retrieved: PropTypes.object,
    loading: PropTypes.bool.isRequired,
    error: PropTypes.string,
    eventSource: PropTypes.instanceOf(EventSource),
    deletedItem: PropTypes.object,
    list: PropTypes.func.isRequired,
    reset: PropTypes.func.isRequired,
    update: PropTypes.func.isRequired,
  };

  componentDidMount() {
    this.props.list(this.props.epic['@id']);
  }

  componentWillUnmount() {
    this.props.reset(this.props.eventSource);
  }

  onEstimateChange(workitem, event) {
    const value = event.target.value.length > 0 ? parseFloat(event.target.value) : null;
    const estimateFieldName = `estimate${upperFirst(event.target.name)}`;
    this.props.update(workitem, {[estimateFieldName]: value});
  }

  getDependencyManagerClass(workitem) {
    const targetWorkitem = this.props.dependencyManager.workitem;
    if (!targetWorkitem || targetWorkitem === workitem) {
      return '';
    }

    const id = workitem['@id'];
    const isDependency = targetWorkitem.dependencies.includes(id);
    const isDependant = targetWorkitem.dependants.includes(id);
    if (isDependency && isDependant) {
      return this.props.classes.dependantDependency;
    }

    if (isDependency) {
      return this.props.classes.dependency;
    }

    if (isDependant) {
      return this.props.classes.dependant;
    }

    return '';
  }

  getBoardCards(workitems) {
    return workitems.map(workitem => {
      return {
        id: workitem['@id'],
        workitem: workitem,
        title: <ExternalLink entity={workitem}/>,
        label: <Estimate workitem={workitem} onChange={this.onEstimateChange.bind(this, workitem)}/>,
        tags: this.getWorkitemTags(workitem),
        className: this.props.classes.card + ' ' + this.getDependencyManagerClass(workitem),
        // Inject our own store to retrieve some action-creator methods in our HOC Card
        // because a 3rd party Card component that will be finally used has its own store as well
        store: store,
      };
    });
  }

  getWorkitems(filterCallback) {
    return this.props.retrieved[this.props.epic['@id']]['hydra:member'].filter(filterCallback);
  }

  getWorkitemTags(workitem) {
    const result = [];
    if (workitem['dependencies'].length > 0) {
      const unfinishedDependencies = this.getUnfinishedDependencies(workitem);
      const tag = {
        title: 'Dependencies: ' + workitem['dependencies'].length,
        bgcolor: '#c51162',
      };
      if (unfinishedDependencies.length) {
        tag.title += ` (${unfinishedDependencies.length})`;
        tag.onClick = () => alert("Unfinished/unassigned dependencies:\n" + this.getWorkitemsInfo(unfinishedDependencies));
      }
      result.push(tag);
    }
    if (workitem['dependants'].length > 0) {
      result.push({
        title: 'Dependants: ' + workitem['dependants'].length,
        bgcolor: '#f57c00'
      });
    }
    return result;
  }

  getUnfinishedDependencies(workitem) {
    if (!this.props.sprints) {
      return []
    }
    const getWorkitemSprint = w => this.props.sprints['hydra:member'].find(s => s['@id'] === w.sprint);
    const workitemSprint = getWorkitemSprint(workitem);
    if (!workitemSprint) {
      return [];
    }

    let unfinishedDependencies = [];
    Object.entries(this.props.retrieved).forEach(([epic, workitems]) => {
      unfinishedDependencies = unfinishedDependencies.concat(workitems['hydra:member'].filter(w => {
        if (!workitem.dependencies.includes(w['@id'])) {
          return false;
        }

        const dependencySprint = getWorkitemSprint(w);
        return !dependencySprint || !w.team || new Date(dependencySprint.endDate) > new Date(workitemSprint.startDate);
      }));
    });

    return unfinishedDependencies;
  }

  getWorkitemsInfo(workitems) {
    return workitems.map(w => {
      const team = this.getWorkitemTeam(w);
      return w.name + ` (${team ? team.name : 'Unassigned'})`;
    }).join("\n");
  }

  getWorkitemTeam(workitem) {
    if (!this.props.teams || !workitem.team) {
      return null;
    }
    return this.props.teams['hydra:member'].find(t => t['@id'] === workitem.team)
  }

  getBoardData() {
    const data = {};
    const columns = [];
    const unassignedColumnWidth = 15;
    const unassignedTeam = {'@id': null, 'name': 'Unassigned'};
    const unassignedSprint = {'@id': null, 'name': 'Unassigned'};

    for (let projectSettings of this.props.programIncrement.projectsSettings) {
      const involvedTeams = this.props.epic.teams;
      let teamsWithCapacity = new Set();
      for (let capacity of projectSettings.teamSprintCapacities) {
        if (involvedTeams.includes(capacity.team)) {
          teamsWithCapacity.add(capacity.team);
        }
      }

      if (!teamsWithCapacity.size) {
        continue;
      }

      let columnsPerRow = projectSettings.sprints.length;
      let columnWith = (100 - unassignedColumnWidth) / columnsPerRow;
      let teams = this.props.teams['hydra:member'].filter(t => teamsWithCapacity.has(t['@id']));
      teams = [unassignedTeam, ...teams];
      const sprints = sortBy(
        this.props.sprints['hydra:member'].filter(s => projectSettings.sprints.includes(s['@id'])),
        o => new Date(o.startDate)
      );
      sprints.unshift(unassignedSprint);
      for (let team of teams) {
        let teamWorkitems = this.getWorkitems(w => w.team === team['@id']);
        if (!teamWorkitems.length && this.props.isReviewModeEnabled) {
          continue;
        }
        for (let sprintIndex in sprints) {
          let sprint = sprints[sprintIndex];
          let teamSprintWorkitems = teamWorkitems.filter(w => w.sprint === sprint['@id']);
          columns.push({
            id: [this.props.epic['@id'], projectSettings.project, team['@id'], sprint['@id']].join(':'),
            title: 'Unassigned' === sprint.name ? team.name : '',
            label: 'Unassigned' === sprint.name ? sprint.name : 'S' + sprintIndex,
            cards: this.getBoardCards(teamSprintWorkitems),
            style: {
              width: `${'Unassigned' === sprint.name ? unassignedColumnWidth : columnWith}%`
            }
          });
        }
      }
    }

    data.lanes = columns;
    return data;
  }

  onDragEnd(cardId, sourceLaneId, targetLaneId) {
    // TODO: Replace with extracting info from a lane object
    const [, sourceProject] = sourceLaneId.split(':')
    const [, targetProject, targetTeam, targetSprint] = targetLaneId.split(':')
    const workitem = this.props.retrieved[this.props.epic['@id']]['hydra:member']
      .find(w => w['@id'] === cardId);
    let patch = {
      project: targetProject,
      team: targetTeam ? targetTeam : null,
      sprint: targetSprint ? targetSprint : null
    };
    if (targetProject !== sourceProject) {
      patch.backlogGroup = this.props.programIncrement.defaultBacklogGroup;
    }
    this.props.update(workitem, patch);
  }

  render() {
    return (
      <div className={this.props.classes.root}>
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

        {this.props.retrieved && this.props.retrieved[this.props.epic['@id']] && this.props.teams && this.props.sprints && (
          <Board id={this.props.epic['@id']} data={this.getBoardData()} editable={!this.props.isReviewModeEnabled}
                 laneDraggable={false} hideCardDeleteIcon={true} handleDragEnd={this.onDragEnd.bind(this)}
                 components={{NewCardForm: NewWorkitemForm, Card: Card}}/>
        )}
      </div>
    );
  }
}

const mapStateToProps = (state) => {
  const {
    retrieved,
    loading,
    error,
    eventSource,
    deletedItem
  } = state.workitem.list;

  return {
    isReviewModeEnabled: state.programincrement.show.isReviewModeEnabled,
    dependencyManager: state.programincrement.show.dependencyManager,
    projects: state.project.list.retrieved['hydra:member'],
    teams: state.team.list.retrieved,
    sprints: state.sprint.list.retrieved,
    retrieved: retrieved,
    loading,
    error,
    eventSource,
    deletedItem
  };
};

const mapDispatchToProps = dispatch => ({
  list: epic => dispatch(list(epic)),
  reset: eventSource => dispatch(reset(eventSource)),
  update: (workitem, values) => dispatch(update(workitem, values)),
});

export default connect(mapStateToProps, mapDispatchToProps)(withStyles(styles)(List));
