package model.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import model.vo.PostTagVO
	import com.forum.config.Config;

	public class PostTagService extends AbstractService
	{
		public function PostTagService()
		{
			super("PostTagService", IConfig(new Config()).AMF_GATEWAY, PostTagVO);
		}
	}
}