
	// 百度地图API功能	
	map = new BMap.Map("allmap");
	map.centerAndZoom(new BMap.Point(121.57957057485,31.16062302186), 15);
	map.enableScrollWheelZoom();   //启用滚轮放大缩小，默认禁用
	map.enableContinuousZoom();    //启用地图惯性拖拽，默认禁用
	var data_info = [[121.57957057485,31.16062302186,"<a href='http://baidu.com' target='_blank'>康桥镇国权商店,御青路901弄158号</a>"],
					 [121.58009617341,31.156079407596,"<a href='http://baidu.com' target='_blank'>御桥便利店,上海市浦东新区御青路98号</a>"]
					];
	var opts = {
				width : 250,     // 信息窗口宽度
				height: 80,     // 信息窗口高度
				title : "信息窗口" , // 信息窗口标题
				enableMessage:true//设置允许信息窗发送短息
			   };
	for(var i=0;i<data_info.length;i++){
		var marker = new BMap.Marker(new BMap.Point(data_info[i][0],data_info[i][1]));  // 创建标注
		var content = data_info[i][2];
		map.addOverlay(marker);               // 将标注添加到地图中
		marker.addEventListener("click",function(e){openInfo(content,e)});
	}
	function openInfo(content,e){
		var p = e.target;
		var point = new BMap.Point(p.getPosition().lng, p.getPosition().lat);
		var infoWindow = new BMap.InfoWindow(content,opts);  // 创建信息窗口对象 
		map.openInfoWindow(infoWindow,point); //开启信息窗口		
	}
