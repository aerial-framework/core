package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.TopicVO;
	import org.aerial.config;

	public class TopicService extends AbstractService
	{
		public function TopicService()
		{
			super("TopicService", IConfig(new Config()).AMF_GATEWAY, TopicVO);
		}
	}
}