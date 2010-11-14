package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.TopicTagVO;
	import org.aerial.config.Config;

	public class TopicTagService extends AbstractService
	{
		public function TopicTagService()
		{
			super("TopicTagService", Config.SERVER_URL, TopicTagVO);
		}
	}
}