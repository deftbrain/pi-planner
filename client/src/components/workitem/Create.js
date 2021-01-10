import React, {useState} from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import MenuItem from '@material-ui/core/MenuItem';
import InputLabel from '@material-ui/core/InputLabel';
import Select from '@material-ui/core/Select';
import FormControl from '@material-ui/core/FormControl'
import Button from '@material-ui/core/Button';
import TextField from '@material-ui/core/TextField';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import DialogContentText from '@material-ui/core/DialogContentText';
import DialogTitle from '@material-ui/core/DialogTitle';
import Snackbar from '@material-ui/core/Snackbar';
import {makeStyles} from '@material-ui/core/styles';
import {store} from '../../store';
import {create, error, reset} from '../../actions/workitem/create';

const useStyles = makeStyles(theme => ({
  formControl: {
    marginTop: theme.spacing(1),
    minWidth: 200,
  },
  selectEmpty: {
    marginTop: theme.spacing(2),
  },
  estimateField: {
    width: 100,
  },
  info: {
    padding: `${theme.spacing(2)}px 0`,
  },
  error: {
    color: theme.palette.error.dark,
    padding: `${theme.spacing(2)}px 0`
  },
}));

const Create = props => {
  const classes = useStyles();
  const [isDialogOpen, setIsDialogOpen] = useState(true);
  const [name, setName] = useState('');
  const [estimateFrontend, setEstimateFrontend] = useState('');
  const [estimateBackend, setEstimateBackend] = useState('');
  const [backlogGroup, setBacklogGroup] = useState('');
  const [epic, project, team, sprint] = props.laneId.split(':');
  const epicDescription = props.epics.retrieved['hydra:member'].find(e => e['@id'] === epic).description;

  const copyTextToNameField = (e) => {
    setName(e.target.innerText.split("\n")[0]);
  }

  const closeDialog = () => {
    props.reset();
    setIsDialogOpen(false);
    props.onCancel();
  }

  const onCreate = () => {
    if (!name || !backlogGroup) {
      props.showError('Name and Backlog Group fields are required!');
      return;
    }
    props.create({
      name,
      backlogGroup,
      estimateFrontend: estimateFrontend.length > 0 ? parseFloat(estimateFrontend) : null,
      estimateBackend: estimateBackend.length > 0 ? parseFloat(estimateBackend) : null,
      status: props.programIncrement.retrieved.projectsSettings.find(ps => ps.project === project).defaultWorkitemStatus,
      project,
      epic,
      team: team || null,
      sprint: sprint || null
    }).catch(e => {
    });
    // Don't call props.onAdd() to avoid duplicated cards on a board (server sends updates pretty quickly)
  }

  function onSnackbarClose() {
    props.reset();
    setName('');
  }

  return (
    <>
      <Snackbar open={!!props.created} autoHideDuration={2000} onClose={onSnackbarClose}
                anchorOrigin={{vertical: 'top', horizontal: 'center'}}
                message="The story has been successfully crated"/>
      <Dialog open={isDialogOpen} fullWidth={true} maxWidth="md" onClose={closeDialog}
              onEntered={() => document.getElementById('epic-description').scrollIntoView({
                block: 'end',
                behavior: 'smooth'
              })}>
        <DialogTitle id="form-dialog-title">Create a story <small>(click the needed line from the epic description below
          to set it as a name for the new story)</small></DialogTitle>
        <DialogContent>
          <DialogContentText>
          </DialogContentText>
          <div id="epic-description" dangerouslySetInnerHTML={{__html: epicDescription}} onClick={copyTextToNameField}/>
        </DialogContent>
        <DialogActions>
          <TextField type="text" value={name} onChange={e => setName(e.target.value)}
                     label="Name" autoFocus margin="dense" fullWidth/>
          <TextField type="number" value={estimateFrontend} onChange={e => setEstimateFrontend(e.target.value)}
                     label="Front" inputProps={{min: 0, step: 0.5}} onFocus={e => e.target.select()}
                     classes={{root: classes.estimateField}} margin="dense"/>
          <TextField type="number" value={estimateBackend} onChange={e => setEstimateBackend(e.target.value)}
                     label="Back" inputProps={{min: 0, step: 0.5}} onFocus={e => e.target.select()}
                     classes={{root: classes.estimateField}} margin="dense"/>
          <FormControl className={classes.formControl}>
            <InputLabel id="story-name-label">Backlog Group</InputLabel>
            <Select value={backlogGroup} onChange={e => setBacklogGroup(e.target.value)} labelId="story-name-label"
                    className={classes.selectEmpty}>
              {props.backlogGroups.retrieved && props.backlogGroups.retrieved['hydra:member']
                .filter(bg => bg.projects.includes(project)).map(backlogGroup =>
                  <MenuItem key={backlogGroup['@id']} value={backlogGroup['@id']}>{backlogGroup['name']}</MenuItem>
                )}
            </Select>
          </FormControl>
          <Button onClick={closeDialog} color="primary">Close</Button>
          <Button disabled={props.loading} onClick={onCreate}
                  color="primary">{(props.loading && 'Creating...') || 'Create'}</Button>
          {props.error && <div className={classes.error}>{props.error}</div>}
        </DialogActions>
      </Dialog>
    </>
  );
}

Create.propTypes = {
  onCancel: PropTypes.func.isRequired,
  onAdd: PropTypes.func.isRequired,
  backlogGroups: PropTypes.object,
}

Create.defaultProps = {}

const mapStateToProps = state => ({
  programIncrement: state.programincrement.show,
  backlogGroups: state.backlogGroup.list,
  epics: state.epic.list,
  loading: state.workitem.create.loading,
  error: state.workitem.create.error,
  created: state.workitem.create.created,
});

const mapDispatchToProps = dispatch => ({
  create: workitem => dispatch(create(workitem)),
  showError: (message) => dispatch(error(message)),
  reset: () => dispatch(reset()),
})

const ConnectedComponent = connect(mapStateToProps, mapDispatchToProps)(Create);
// Pass our store directly because this component is used as a child component
// inside a 3rd party component that has its own Redux store
export default (props) => <ConnectedComponent {...props} store={store}/>;
