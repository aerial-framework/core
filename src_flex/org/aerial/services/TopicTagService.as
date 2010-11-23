package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.TopicTagVO;

	public class TopicTagService extends AbstractService
	{
		public function TopicTagService()
		{
			super("TopicTagService", "http://aerial-test/server.php", TopicTagVO);
		}
	}
}