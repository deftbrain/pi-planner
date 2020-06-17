import React from 'react';
import PropTypes from 'prop-types';
import {withStyles} from '@material-ui/core/styles';

class Estimate extends React.Component {
  static propTypes = {
    workitem: PropTypes.object.isRequired,
    onChange: PropTypes.func.isRequired,
    classes: PropTypes.object.isRequired,
  }

  constructor(props) {
    super(props);
    this.estimateFrontend = React.createRef();
    this.estimateBackend = React.createRef();
  }

  componentDidUpdate(prevProps) {
    for (let prop of ['estimateFrontend', 'estimateBackend']) {
      if (prevProps.workitem[prop] !== this.props.workitem[prop]) {
        this[prop].current.value = this.props.workitem[prop];
      }
    }
  }

  render() {
    return (
      <form>
        <input type="text" name="frontend" className={this.props.classes.input} placeholder="F"
               onChange={this.props.onChange} onFocus={e => e.target.select()} title="Frontend"
               ref={this.estimateFrontend} defaultValue={this.props.workitem.estimateFrontend}/>
        <input type="text" name="backend" className={this.props.classes.input} placeholder="B"
               onChange={this.props.onChange} onFocus={e => e.target.select()} title="Backend"
               ref={this.estimateBackend} defaultValue={this.props.workitem.estimateBackend}/>
      </form>
    );
  }
}

const styles = {
  input: {
    width: '30px',
    textAlign: 'center',
  },
};

export default withStyles(styles)(Estimate);
