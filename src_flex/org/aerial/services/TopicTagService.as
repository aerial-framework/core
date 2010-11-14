package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.TopicTagVO;
	import org.aerial.config;

	public class TopicTagService extends AbstractService
	{
		public function TopicTagService()
		{
			super("TopicTagService", IConfig(new Config()).AMF_GATEWAY, TopicTagVO);
		}
	}
}