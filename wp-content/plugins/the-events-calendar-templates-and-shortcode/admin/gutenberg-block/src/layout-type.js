
const LayoutType=(props)=>{
	if(!props.layout){
		return null;
	}
	if(props.layout=="timeline-view"){
		return <div className="event-template">
		<img src={props.LayoutImgPath+"/events-timeline-view.png"} />
		</div>;
	}else if(props.layout=="grid-view"){
		return <div className="event-template">
		<img src={props.LayoutImgPath+"/events-grid-view.png"} />
		</div>;
	}else if(props.layout=="carousel-view"){
		return <div className="event-template">
		<img src={props.LayoutImgPath+"/events-carousel-view.png"} />
		</div>;
	}else if(props.layout=="slider-view"){
		return <div className="event-template">
		<img src={props.LayoutImgPath+"/events-slider-view.png"} />
		</div>;
	}else if(props.layout=="masonry-view"){
		return <div className="event-template">
		<img src={props.LayoutImgPath+"/events-masonry-view.png"} />
		</div>;
	}
	else {
		return <div className="event-template">
		<img src={props.LayoutImgPath+"/events-list-view.png"} />
		</div>;
	}	
}
export default LayoutType;