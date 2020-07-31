
const LayoutType=(props)=>{
	if(!props.layout){
		return null;
	}
	if(props.layout=="timeline-view"){
		return <div className="event-template">
		<img src={props.LayoutImgPath+"/events-timeline-view.png"} />
		</div>;
	}
	else {
		return <div className="event-template">
		<img src={props.LayoutImgPath+"/events-list-view.png"} />
		</div>;
	}	
}
export default LayoutType;