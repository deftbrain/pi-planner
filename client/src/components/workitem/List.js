import React, {Component} from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import {list, reset} from '../../actions/workitem/list';
import Board from 'react-trello';
import './List.css'

class List extends Component {
  static propTypes = {
    epic: PropTypes.string.isRequired,
    projectSettings: PropTypes.array.isRequired,
    retrieved: PropTypes.object,
    loading: PropTypes.bool.isRequired,
    error: PropTypes.string,
    eventSource: PropTypes.instanceOf(EventSource),
    deletedItem: PropTypes.object,
    list: PropTypes.func.isRequired,
    reset: PropTypes.func.isRequired
  };

  componentDidMount() {
    this.props.list(this.props.epic);
  }

  componentWillUnmount() {
    this.props.reset(this.props.eventSource);
  }

  getBoardCards(project, team, sprint) {
    const workitems = this.props.retrieved['hydra:member']
      .filter(w => w.project == project && w.team == team && w.sprint == sprint);
    return workitems.map(w => { return {id: w['@id'], title: w.name}; });
  }

  getBoardData() {
    const data = {};
    const columns = [];
    const unsignedColumnWidth = 15;
    for (let settings of this.props.projectSettings) {
      let columnsPerRow = settings.sprints.length;
      let columnWith = (100 - unsignedColumnWidth) / columnsPerRow;
      let teams = new Set(settings.capacity.map(c => c.team));
      teams = [null, ...teams];
      let sprints = [null, ...settings.sprints];
      for (let team of teams) {
        for (let index in sprints) {
          let sprint = sprints[index];
          const id = [this.props.epic, settings.project, team, sprint].join(':');
          columns.push({
            id: id,
            title: `${team} ${index}`,
            // label: team,
            cards: this.getBoardCards(settings.project, team, sprint),
            style: {
              width: `${null === sprint ? unsignedColumnWidth : columnWith}%`
            }
          });
        }
      }
    }

    data.lanes = columns;
    return data;
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

        {this.props.retrieved && (
          <Board id={this.props.epic} data={this.getBoardData()} editable={true}/>
        )}
      </div>
    );
  }
}

const mapStateToProps = (state, ownProps) => {
  const {
    retrieved,
    loading,
    error,
    eventSource,
    deletedItem
  } = state.workitem.list;
  return {retrieved: retrieved[ownProps.epic], loading, error, eventSource, deletedItem};
};

const mapDispatchToProps = dispatch => ({
  list: epic => dispatch(list(epic)),
  reset: eventSource => dispatch(reset(eventSource))
});

export default connect(mapStateToProps, mapDispatchToProps)(List);
